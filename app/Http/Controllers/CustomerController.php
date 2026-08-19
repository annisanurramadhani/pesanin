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
    public function showMenu($code)
    {
        $qrCode = QrCode::where('code', $code)->firstOrFail();
        $merchant = $qrCode->merchant;

        $categories = Category::where('merchant_id', $merchant->id)->get();
        $menus = Menu::where('merchant_id', $merchant->id)->get(); 

        return view('customer.menu', compact('qrCode', 'merchant', 'categories', 'menus'));
    }

    // 2. Proses Checkout Pesanan
    public function checkout(Request $request, $code)
    {
        $qrCode = QrCode::where('code', $code)->firstOrFail();

        $customerName = trim($request->customer_name);
        if (empty($customerName)) {
            $customerName = 'Pelanggan ' . ($qrCode->name ?? 'Meja');
        }

        $request->validate([
            'items' => 'required|array|min:1',
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
                ];
            }

            if (empty($orderItemsData)) {
                return back()->with('error', 'Silakan pilih minimal 1 menu sebelum memesan.');
            }

            // Simpan Order
            $order = Order::create([
                'merchant_id'    => $qrCode->merchant_id,
                'qr_code_id'     => $qrCode->id,
                'order_number'   => $orderNumber,
                'customer_name'  => $customerName,
                'total_amount'   => $totalPrice,
                'payment_method' => $request->payment_method ?? 'qris',
                'payment_status' => 'unpaid',
                'status'         => 'pending',
            ]);

            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id'  => $order->id,
                    'menu_id'   => $itemData['menu']->id,
                    'menu_name' => $itemData['menu']->name,
                    'quantity'  => $itemData['quantity'],
                    'price'     => $itemData['price'],
                    'subtotal'  => $itemData['subtotal'],
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