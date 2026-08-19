<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $filterType = $request->get('filter_type', 'day'); // Options: day, month, year
        
        $selectedDate  = $request->get('date', Carbon::today()->toDateString());
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedYear  = $request->get('year', Carbon::now()->year);

        $query = Order::where('merchant_id', $merchantId)
            ->with(['qrCode', 'items.menu']);

        // Filter Spesifik Berdasarkan Mode yang Dipilih
        if ($filterType === 'day') {
            $query->whereDate('created_at', $selectedDate);
            $labelPeriode = Carbon::parse($selectedDate)->format('d M Y');
        } elseif ($filterType === 'month') {
            $carbonMonth = Carbon::parse($selectedMonth);
            $query->whereYear('created_at', $carbonMonth->year)
                  ->whereMonth('created_at', $carbonMonth->month);
            $labelPeriode = $carbonMonth->format('F Y');
        } elseif ($filterType === 'year') {
            $query->whereYear('created_at', $selectedYear);
            $labelPeriode = 'Tahun ' . $selectedYear;
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Hitung Total Pendapatan
        $totalRevenue = $orders->sum(function($order) {
            if ($order->total_amount > 0) return $order->total_amount;
            if ($order->total_price > 0) return $order->total_price;
            
            return $order->items->sum(function($item) {
                return $item->subtotal ?? ($item->price * $item->quantity);
            });
        });

        $totalOrders = $orders->count();

        return view('merchant.orders.index', compact(
            'orders', 
            'filterType', 
            'selectedDate', 
            'selectedMonth', 
            'selectedYear', 
            'labelPeriode', 
            'totalRevenue', 
            'totalOrders'
        ));
    }
}