<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    // Helper Internal Dekripsi (Mencegah Error Karakter Simbol di URL)
    private function resolveId($encryptedId)
    {
        $decoded = urldecode($encryptedId);
        return decryptId($decoded) ?? decryptId($encryptedId) ?? (is_numeric($encryptedId) ? $encryptedId : null);
    }

    // 1. Tampilkan Daftar Menu & Kategori
    public function index(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        $categories = Category::where('merchant_id', $merchantId)->get();
        $menus = Menu::with('category')->where('merchant_id', $merchantId)->get();

        return view('merchant.menu.index', compact('categories', 'menus'));
    }

    // 2. Simpan Kategori Baru
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        Category::create([
            'merchant_id' => $request->user()->merchant_id,
            'name'        => trim($request->name),
            'slug'        => Str::slug($request->name),
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    // 3. Simpan Menu Baru
    public function store(Request $request)
    {
        $merchantId = $request->user()->merchant_id;
        $slug = Str::slug($request->name);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menus', 'slug')->where(function ($query) use ($merchantId) {
                    return $query->where('merchant_id', $merchantId);
                }),
            ],
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.unique' => 'Menu dengan nama tersebut sudah tersedia.',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'merchant_id'  => $merchantId,
            'category_id'  => $request->category_id,
            'name'         => trim($request->name),
            'slug'         => $slug,
            'price'        => $request->price,
            'description'  => $request->description,
            'is_available' => true, // Default langsung ready
            'image'        => $imagePath,
        ]);

        return back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // 4. Halaman Edit Menu
    public function edit(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);
        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $menu = Menu::where('merchant_id', $request->user()->merchant_id)->findOrFail($id);
        $categories = Category::where('merchant_id', $request->user()->merchant_id)->get();

        return view('merchant.menu.edit', compact('menu', 'categories'));
    }

    // 5. Update Menu
    public function update(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);
        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $menu = Menu::where('merchant_id', $request->user()->merchant_id)->findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'category_id'  => $request->category_id,
            'name'         => trim($request->name),
            'slug'         => Str::slug($request->name),
            'price'        => $request->price,
            'description'  => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('merchant.menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    // 6. Hapus Menu
    public function destroy(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);
        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $menu = Menu::where('merchant_id', $request->user()->merchant_id)->findOrFail($id);

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus!');
    }

    // 7. Toggle Status Ready / Habis
    public function toggle(Request $request, $encryptedId)
    {
        $id = $this->resolveId($encryptedId);
        if (!$id) {
            abort(404, 'ID Menu tidak valid.');
        }

        $menu = Menu::where('merchant_id', $request->user()->merchant_id)->findOrFail($id);

        // Pastikan mengambil dari input request atau paksa jadi 'unavailable' jika tombol habis ditekan
        $status = $request->input('status', 'available');

        $menu->update([
            'status' => $status
        ]);

        return back()->with('success', 'Status ketersediaan menu berhasil diubah!');
    }
}
