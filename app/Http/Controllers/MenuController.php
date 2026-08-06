<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Maksimal 2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'merchant_id' => $request->user()->merchant_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Menu baru berhasil ditambahkan!');
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
}