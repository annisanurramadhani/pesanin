<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // 1. Tampilkan Menu saat Scan QR
    public function showMenu($code_hash)
    {
        $qrCode = QrCode::where('code_hash', $code_hash)->firstOrFail();
        $merchant = $qrCode->merchant;

        $categories = Category::where('merchant_id', $merchant->id)->get();
        
        // Hapus where('is_available', true) dan where('stock', '>', 0) agar menu yang habis tetap terkirim ke halaman
        $menus = Menu::where('merchant_id', $merchant->id)->get(); 

        return view('customer.menu', compact('qrCode', 'merchant', 'categories', 'menus'));
    }

    // 2. Proses Checkout Pesanan
    public function checkout(Request $request, $code_hash)
    {
        $qrCode = QrCode::where('code_hash', $code_hash)->firstOrFail();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items'         => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $totalPrice = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $menuId = $item['menu_id'] ?? $item['id'] ?? null;
                $quantity = $item['quantity'] ?? $item['qty'] ?? 0;

                if (!$menuId || $quantity <= 0) {
                    continue;
                }

                $menu = Menu::where('id', $menuId)
                    ->where('merchant_id', $qrCode->merchant_id)
                    ->firstOrFail();

                $subtotal = $menu->price * $quantity;
                $totalPrice += $subtotal;

                $orderItemsData[] = [
                    'menu'     => $menu,
                    'quantity' => $quantity,
                    'price'    => $menu->price,
                    'subtotal' => $subtotal,
                    'notes'    => $item['notes'] ?? null,
                ];
            }

            if (empty($orderItemsData)) {
                return back()->with('error', 'Silakan pilih minimal 1 menu sebelum memesan.');
            }

            // Simpan Order (Otomatis diset ke QRIS)
            $order = Order::create([
                'merchant_id'    => $qrCode->merchant_id,
                'qr_code_id'     => $qrCode->id,
                'order_number'   => $orderNumber,
                'customer_name'  => $request->customer_name,
                'total_amount'   => $totalPrice,
                'payment_method' => 'qris', // Default QRIS
                'payment_status' => 'unpaid',
                'status'         => 'menunggu',
            ]);

            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $itemData['menu']->id,
                    'quantity' => $itemData['quantity'],
                    'price'    => $itemData['price'],
                    'subtotal' => $itemData['subtotal'],
                    'notes'    => $itemData['notes'],
                ]);

                if (isset($itemData['menu']->stock) && $itemData['menu']->stock > 0) {
                    $itemData['menu']->decrement('stock', $itemData['quantity']);
                }
            }

            DB::commit();

            return redirect()->route('customer.success', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    // 3. Halaman Bukti Pesanan Pelanggan
    public function success($order_number)
    {
        $order = Order::where('order_number', $order_number)
            ->with(['merchant', 'qrCode', 'items.menu'])
            ->firstOrFail();

        return view('customer.success', compact('order'));
    }
}