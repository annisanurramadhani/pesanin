<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page
Route::get('/', function () {
    return view('welcome');
});

// 2. Rute Pelanggan (Scan QR & Order)
Route::prefix('m')->group(function () {
    Route::get('/{code_hash}', [CustomerController::class, 'showMenu'])->name('customer.menu');
    Route::post('/{code_hash}/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
});
Route::get('/order/success/{order_number}', [CustomerController::class, 'success'])->name('customer.success');

// 3. Redirect Dashboard Default
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'dapur') {
        return redirect()->route('merchant.orders.index');
    }

    return redirect()->route('merchant.dashboard');
})->middleware(['auth'])->name('dashboard');


// 4. Rute Khusus SUPER ADMIN
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});


// 5. Rute Khusus MERCHANT (Owner, Kasir, Dapur)
Route::middleware(['auth'])->prefix('merchant')->name('merchant.')->group(function () {

    // 1. Dashboard (Hanya Owner & Kasir)
    // Khusus OWNER (QR Code & Menu)
    Route::middleware(['role:owner,super_admin'])->group(function () {
        Route::get('/qr', [MerchantController::class, 'qrIndex'])->name('qr.index');
        Route::post('/qr', [MerchantController::class, 'qrStore'])->name('qr.store');
        Route::get('/qr/{qrCode}/print', [MerchantController::class, 'qrPrint'])->name('qr.print'); // <-- TAMBAHKAN BARIS INI

        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::post('/category', [MenuController::class, 'storeCategory'])->name('category.store');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    });

    // 2. Kelola Pesanan (Owner, Kasir, & Dapur BISA AKSES)
    Route::middleware(['role:owner,kasir,dapur,super_admin'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/check', [OrderController::class, 'checkNew'])->name('orders.check'); // <-- Tambahkan baris ini
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    });

    // 3. Kelola QR Code & Menu (KHUSUS OWNER)
    Route::middleware(['role:owner,super_admin'])->group(function () {
        Route::get('/qr', [MerchantController::class, 'qrIndex'])->name('qr.index');
        Route::post('/qr', [MerchantController::class, 'qrStore'])->name('qr.store');

        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::post('/category', [MenuController::class, 'storeCategory'])->name('category.store');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    });

});

// Profile User
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';