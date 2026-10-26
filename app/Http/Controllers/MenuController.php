<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Merchant;
use App\Services\SubscriptionLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class MenuController extends Controller
{
    /**
     * Mendekripsi ID dari URL.
     */
    private function resolveId($encryptedId)
    {
        $decoded = urldecode($encryptedId);

        return decryptId($decoded)
            ?? decryptId($encryptedId)
            ?? (is_numeric($encryptedId) ? $encryptedId : null);
    }

    /**
     * Menentukan disk tempat gambar berada.
     * Mendukung gambar baru di S3 dan gambar lama di lokal.
     */
    private function findImageDisk(string $path): ?string
    {
        if (Storage::disk('s3_public')->exists($path)) {
            return 's3_public';
        }

        if (Storage::disk('public')->exists($path)) {
            return 'public';
        }

        return null;
    }

    /**
     * Menghapus gambar dengan penanganan kegagalan.
     */
    private function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            $disk = $this->findImageDisk($path);

            if ($disk) {
                Storage::disk($disk)->delete($path);
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * 1. Tampilkan daftar menu dan kategori.
     */
    public function index(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        $categories = Category::where('merchant_id', $merchantId)
            ->get();

        $menus = Menu::with('category')
            ->where('merchant_id', $merchantId)
            ->get();

        return view(
            'merchant.menu.index',
            compact('categories', 'menus')
        );
    }

    /**
     * 2. Simpan kategori baru.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $merchantId = $request->user()->merchant_id;

        if (!$merchantId) {
            return back()->with(
                'error',
                'Akun kamu belum terhubung ke merchant mana pun.'
            );
        }

        Category::create([
            'merchant_id' => $merchantId,
            'name' => trim($request->name),
            'slug' => Str::slug($request->name),
        ]);

        return back()->with(
            'success',
            'Kategori berhasil ditambahkan!'
        );
    }

    /**
     * 3. Hapus kategori.
     */
    public function destroyCategory(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);

        if (!$id) {
            abort(404, 'ID Kategori tidak valid.');
        }

        $merchantId = $request->user()->merchant_id;

        $category = Category::where('merchant_id', $merchantId)
            ->findOrFail($id);

        if ($category->menus()->exists()) {
            return back()->with(
                'error',
                'Kategori masih memiliki menu. Hapus semua menu terlebih dahulu.'
            );
        }

        $category->delete();

        return back()->with(
            'success',
            'Kategori berhasil dihapus!'
        );
    }

    /**
     * 4. Simpan menu baru.
     */
    public function store(
        Request $request,
        SubscriptionLimitService $limitService
    ) {
        $merchantId = $request->user()->merchant_id;

        if (!$merchantId) {
            return back()->with(
                'error',
                'Akun kamu belum terhubung ke merchant mana pun.'
            );
        }

        $merchant = Merchant::findOrFail($merchantId);

        $currentMenuCount = Menu::where(
            'merchant_id',
            $merchantId
        )->count();

        try {
            $limitService->ensureCanCreateMenu(
                $merchant,
                $currentMenuCount
            );
        } catch (\RuntimeException $e) {
            return back()->with('limit_reached', [
                'type' => 'menu',
                'title' => 'Batas Menu Tercapai',
                'message' => $e->getMessage(),
            ]);
        }

        $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('merchant_id', $merchantId),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menus', 'slug')
                    ->where(function ($query) use ($merchantId) {
                        return $query->where(
                            'merchant_id',
                            $merchantId
                        );
                    }),
            ],
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.unique' => 'Menu dengan nama tersebut sudah tersedia.',
        ]);

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store(
                    'menus',
                    's3_public'
                );

                if (!$imagePath) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Gagal mengunggah gambar menu ke penyimpanan.'
                        );
                }
            }

            DB::transaction(function () use (
                $merchantId,
                $request,
                &$imagePath
            ) {
                Menu::create([
                    'merchant_id' => $merchantId,
                    'category_id' => $request->category_id,
                    'name' => trim($request->name),
                    'slug' => Str::slug($request->name),
                    'price' => $request->price,
                    'description' => $request->description,
                    'is_available' => true,
                    'image' => $imagePath,
                ]);
            });
        } catch (Throwable $e) {
            if ($imagePath) {
                $this->deleteImage($imagePath);
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Menu gagal disimpan. Silakan coba kembali.'
                );
        }

        return back()->with(
            'success',
            'Menu berhasil ditambahkan!'
        );
    }

    /**
     * 5. Halaman edit menu.
     */
    public function edit(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);

        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $merchantId = $request->user()->merchant_id;

        $menu = Menu::where('merchant_id', $merchantId)
            ->findOrFail($id);

        $categories = Category::where('merchant_id', $merchantId)
            ->get();

        return view(
            'merchant.menu.edit',
            compact('menu', 'categories')
        );
    }

    /**
     * 6. Update menu.
     */
    public function update(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);

        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $merchantId = $request->user()->merchant_id;

        $menu = Menu::where('merchant_id', $merchantId)
            ->findOrFail($id);

        $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('merchant_id', $merchantId),
            ],
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name' => trim($request->name),
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'description' => $request->description,
        ];

        $oldImagePath = $menu->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store(
                    'menus',
                    's3_public'
                );

                if (!$newImagePath) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Gagal mengunggah gambar menu ke penyimpanan.'
                        );
                }

                $data['image'] = $newImagePath;
            }

            DB::transaction(function () use ($menu, $data) {
                $menu->update($data);
            });
        } catch (Throwable $e) {
            if ($newImagePath) {
                $this->deleteImage($newImagePath);
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Menu gagal diperbarui. Silakan coba kembali.'
                );
        }

        // Hapus gambar lama hanya setelah database berhasil diperbarui.
        if ($newImagePath && $oldImagePath) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()
            ->route('merchant.menu.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * 7. Hapus menu.
     */
    public function destroy(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);

        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $menu = Menu::where(
            'merchant_id',
            $request->user()->merchant_id
        )->findOrFail($id);

        $imagePath = $menu->image;

        try {
            DB::transaction(function () use ($menu) {
                $menu->delete();
            });
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Menu gagal dihapus. Silakan coba kembali.'
            );
        }

        // Bersihkan gambar setelah penghapusan database berhasil.
        $this->deleteImage($imagePath);

        return back()->with(
            'success',
            'Menu berhasil dihapus!'
        );
    }

    /**
     * 8. Toggle status ready/habis.
     */
    public function toggle(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);

        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $menu = Menu::where(
            'merchant_id',
            $request->user()->merchant_id
        )->findOrFail($id);

        $request->validate([
            'status' => 'sometimes|in:available,unavailable',
        ]);

        $status = $request->input('status', 'available');

        $menu->update([
            'status' => $status,
        ]);

        return back()->with(
            'success',
            'Status ketersediaan menu berhasil diubah!'
        );
    }
}
