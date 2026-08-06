<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // Tampilkan Menu Pelanggan berdasarkan QR Code Hash
    public function showMenu($code_hash)
    {
        // Ambil QR Code beserta relasi merchant
        $qrCode = QrCode::with('merchant')->where('code_hash', $code_hash)->where('is_active', true)->firstOrFail();

        // Ambil Kategori dan Menu milik Merchant tersebut
        $categories = Category::with('menus')
            ->where('merchant_id', $qrCode->merchant_id)
            ->get();

        return view('customer.menu', compact('qrCode', 'categories'));
    }

    // Proses Checkout Order Pelanggan
    public function checkout(Request $request, $code_hash)
    {
        $qrCode = QrCode::where('code_hash', $code_hash)->firstOrFail();

        $request->validate([
            'customer_name' => 'required|string|max:100',
            'items' => 'required|array',
        ]);

        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $menuId => $quantity) {
            if ($quantity > 0) {
                $menu = \App\Models\Menu::find($menuId);
                if ($menu) {
                    $subtotal = $menu->price * $quantity;
                    $totalAmount += $subtotal;

                    $orderItems[] = [
                        'menu_id' => $menu->id,
                        'quantity' => $quantity,
                        'price' => $menu->price,
                    ];
                }
            }
        }

        if (empty($orderItems)) {
            return back()->with('error', 'Silakan pilih minimal 1 menu.');
        }

        // Buat Order Baru
        $order = Order::create([
            'merchant_id' => $qrCode->merchant_id,
            'qr_code_id' => $qrCode->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(6)),
            'customer_name' => $request->customer_name,
            'total_amount' => $totalAmount,
            'status' => 'menunggu',
        ]);

        // Simpan Detail Items
        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return redirect()->route('customer.success', $order->order_number);
    }

    // Halaman Selesai Order
    public function success($order_number)
    {
        $order = Order::with(['qrCode', 'items.menu'])->where('order_number', $order_number)->firstOrFail();
        return view('customer.success', compact('order'));
    }
}