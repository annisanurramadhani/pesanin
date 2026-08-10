<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    // 1. Tampilkan Menus saat Scan QR
    public function showMenu($code_hash)
    {
        $qrCode = QrCode::where('code_hash', $code_hash)->firstOrFail();
        $merchant = $qrCode->merchant;

        // Ambil kategori beserta menu aktifnya
        $categories = Category::where('merchant_id', $merchant->id)->get();
        $menus = Menu::where('merchant_id', $merchant->id)
            ->where('is_available', true)
            ->get();

        return view('customer.menu', compact('qrCode', 'merchant', 'categories', 'menus'));
    }

    // 2. Proses Checkout Pesanan Pelanggan
   public function checkout(Request $request, $code_hash)
    {
        $qrCode = QrCode::where('code_hash', $code_hash)->firstOrFail();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Generate Nomor Order Unik
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -3));

            $totalPrice = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $menu = Menu::where('id', $item['menu_id'])
                    ->where('merchant_id', $qrCode->merchant_id)
                    ->firstOrFail();

                $subtotal = $menu->price * $item['quantity'];
                $totalPrice += $subtotal;

                $orderItemsData[] = [
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Simpan Data Order
            $order = Order::create([
                'merchant_id' => $qrCode->merchant_id,
                'qr_code_id' => $qrCode->id,
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // Simpan Detail Items
            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            DB::commit();

            return redirect()->route('customer.success', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    // 3. Halaman Sukses Setelah Pesan
    public function success($order_number)
    {
        $order = Order::where('order_number', $order_number)
            ->with(['merchant', 'qrCode', 'items.menu'])
            ->firstOrFail();

        return view('customer.success', compact('order'));
    }
}