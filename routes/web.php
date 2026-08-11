<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;

use App\Http\Controllers\Admin\MerchantController as AdminMerchantController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page
Route::get('/', function () {
    return view('welcome');
});

// 2. Rute Pelanggan (Scan QR & Order) - SUDAH DIPERBAIKI (Tidak dobel /m/m lagi)
Route::prefix('m')->group(function () {
    Route::get('{code_hash}', [CustomerController::class, 'showMenu'])->name('customer.menu');
    Route::post('{code_hash}/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
});
Route::get('/order/success/{order_number}', [CustomerController::class, 'success'])->name('customer.success');

// 3. Redirect Dashboard Default Berdasarkan Role
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
    Route::get('/merchants', [AdminMerchantController::class, 'index'])
            ->name('merchants.index');

    Route::get('/merchants/create', [AdminMerchantController::class, 'create'])
            ->name('merchants.create');

    Route::post('/merchants', [AdminMerchantController::class, 'store'])
            ->name('merchants.store');

    Route::get('/merchants/{encryptedId}/edit', [AdminMerchantController::class, 'edit'])
            ->name('merchants.edit');

    Route::put('/merchants/{encryptedId}', [AdminMerchantController::class, 'update'])
            ->name('merchants.update');

    Route::delete('/merchants/{encryptedId}', [AdminMerchantController::class, 'destroy'])
            ->name('merchants.destroy');
});

// 5. Rute MERCHANT (Owner, Kasir, Dapur)
Route::middleware(['auth'])->prefix('merchant')->name('merchant.')->group(function () {

    // Dashboard Merchant (Owner & Kasir)
    Route::middleware(['role:owner,kasir'])->group(function () {
        Route::get('/dashboard', [MerchantController::class, 'dashboard'])->name('dashboard');
    });

    // Kelola Pesanan (Owner, Kasir, Dapur)
    Route::middleware(['role:owner,kasir,dapur'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/check', [OrderController::class, 'checkNew'])->name('orders.check');
        Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    });

    // Khusus OWNER (QR Code, Menu, Profil Kafe, & Staf)
    Route::middleware(['role:owner'])->group(function () {
        // QR Code Meja
        Route::get('/qr', [MerchantController::class, 'qrIndex'])->name('qr.index');
        Route::post('/qr', [MerchantController::class, 'qrStore'])->name('qr.store');
        Route::get('/qr/{qrCode}/print', [MerchantController::class, 'qrPrint'])->name('qr.print');
        Route::delete('/qr/{qrCode}', [MerchantController::class, 'qrDestroy'])->name('qr.destroy');

        // Menu & Kategori (Sudah ditambah rute Update & Toggle Status Ready/Habis)
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::post('/category', [MenuController::class, 'storeCategory'])->name('category.store');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::patch('/menu/{menu}/toggle', [MenuController::class, 'toggleStatus'])->name('menu.toggle');
        Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
        Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');

        // Kelola Profil Kafe (Owner)
        Route::get('/profile-kafe', [MerchantController::class, 'profileEdit'])->name('profile-kafe.edit');
        Route::put('/profile-kafe', [MerchantController::class, 'profileUpdate'])->name('profile-kafe.update');

        // Kelola Staf Kafe
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::delete('/staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

});

// 6. Profile User Bawaan Auth
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
