<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
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
            'name' => $request->name,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    // 3. Simpan Menu Baru
    public function storeMenu(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Menu::create([
            'merchant_id' => $request->user()->merchant_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'is_available' => true,
        ]);

        return back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // Store Menu Baru
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0', // <-- Validasi Stok
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'merchant_id'  => $request->user()->merchant_id,
            'category_id'  => $request->category_id,
            'name'         => $request->name,
            'price'        => $request->price,
            'stock'        => $request->stock, // <-- Simpan Stok
            'description'  => $request->description,
            'image'        => $imagePath,
            'is_available' => $request->stock > 0, // Otomatis ready jika stok > 0
        ]);

        return back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // Update Menu
    public function update(Request $request, Menu $menu)
    {
        if ($menu->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0', // <-- Validasi Stok
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->stock, // <-- Update Stok
            'description' => $request->description,
            'is_available' => $request->stock > 0,
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

    // 4. Hapus Menu
    public function destroyMenu(Request $request, Menu $menu)
    {
        if ($menu->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus!');
    }

    public function edit(Request $request, Menu $menu)
    {
        if ($menu->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $categories = Category::where('merchant_id', $request->user()->merchant_id)->get();

        return view('merchant.menu.edit', compact('menu', 'categories'));
    }
}