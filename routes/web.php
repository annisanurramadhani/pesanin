<?php

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Auth\VerifyEmailController;

use App\Http\Controllers\Merchant\StaffController;

use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\MidtransNotificationController;
use App\Http\Controllers\Payment\MidtransOrderNotificationController;
use App\Http\Controllers\Customer\CustomerOrderController;

use App\Http\Controllers\Auth\EmailVerificationController;

use App\Http\Controllers\Merchant\MerchantSetupController;
use App\Http\Controllers\Merchant\DashboardController;

use App\Http\Controllers\PublicSubscription\PublicSubscriptionController;
use App\Http\Controllers\Superadmin\PackageController;
use App\Http\Controllers\Superadmin\PackageDurationController;
use App\Http\Controllers\SuperAdmin\MerchantController as SuperAdminMerchantController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperAdminSubscriptionController;
use App\Http\Controllers\SuperAdmin\AccountController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// ==========================================================================
// 1. LANDING PAGE
// ==========================================================================

Route::get('/', function () {
    return view('welcome');
});


// ==========================================================================
// 2. PUBLIC SUBSCRIPTION
// ==========================================================================

Route::prefix('subscription')
    ->name('public.subscription.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Halaman utama subscription
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [PublicSubscriptionController::class, 'index']
        )->name('index');


        /*
        |--------------------------------------------------------------------------
        | Lanjut pembayaran setelah login
        |--------------------------------------------------------------------------
        |
        | Route ini digunakan ketika:
        |
        | Guest
        | -> pilih paket
        | -> pilih durasi
        | -> ringkasan
        | -> sudah punya akun
        | -> login
        | -> kembali ke sini
        |
        */

        Route::get(
            '/account/continue',
            [PublicSubscriptionController::class, 'continuePayment']
        )->name('account.continue');


        /*
        |--------------------------------------------------------------------------
        | Sudah punya akun / belum punya akun
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/account/{encryptedDuration}',
            [PublicSubscriptionController::class, 'account']
        )->name('account');


        /*
        |--------------------------------------------------------------------------
        | Ringkasan subscription
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{slug}/summary/{duration}',
            [PublicSubscriptionController::class, 'summary']
        )->name('summary');


        /*
        |--------------------------------------------------------------------------
        | Pilih durasi
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{slug}',
            [PublicSubscriptionController::class, 'show']
        )->name('show');
    });


// ==========================================================================
// 3. CUSTOMER ORDER
// ==========================================================================

Route::prefix('m/{code}')
    ->name('customer.')
    ->group(function () {

        Route::get(
            '/',
            [CustomerOrderController::class, 'menu']
        )->name('menu');


        Route::get(
            '/cart',
            [CustomerOrderController::class, 'cart']
        )->name('cart');


        Route::post(
            '/cart/add',
            [CustomerOrderController::class, 'addToCart']
        )->name('cart.add');


        Route::patch(
            '/cart/update',
            [CustomerOrderController::class, 'updateCart']
        )->name('cart.update');


        Route::delete(
            '/cart/{menuId}',
            [CustomerOrderController::class, 'removeFromCart']
        )->name('cart.remove');


        Route::get(
            '/checkout',
            [CustomerOrderController::class, 'checkout']
        )->name('checkout');


        Route::post(
            '/checkout',
            [CustomerOrderController::class, 'store']
        )->name('checkout.store');


        Route::get(
            '/order/{orderNumber}',
            [CustomerOrderController::class, 'success']
        )->name('order.success');


        Route::get(
            '/order/{orderNumber}/payment',
            [CustomerOrderController::class, 'payment']
        )->name('payment');
    });


// ==========================================================================
// 4. MIDTRANS ORDER NOTIFICATION
// ==========================================================================

Route::post(
    '/payment/midtrans/order/notification',
    [MidtransOrderNotificationController::class, 'handle']
)->name('payment.midtrans.order.notification');


// ==========================================================================
// 5. DEFAULT DASHBOARD
// ==========================================================================

Route::get('/dashboard', function () {

    $user = Auth::user();


    if ($user->role === 'super_admin') {

        return redirect()->route(
            'super_admin.dashboard'
        );
    }


    if ($user->role === 'dapur') {

        return redirect()->route(
            'merchant.orders.index'
        );
    }


    return redirect()->route(
        'merchant.dashboard'
    );

})
    ->middleware('auth')
    ->name('dashboard');


// ==========================================================================
// 6. SUPER ADMIN
// ==========================================================================

Route::middleware([
    'auth',
    'role:super_admin'
])
    ->prefix('super_admin')
    ->name('super_admin.')
    ->group(function () {


        // ==================================================================
        // Dashboard
        // ==================================================================

        Route::get(
            '/dashboard',
            function () {
                return view('super_admin.dashboard');
            }
        )->name('dashboard');


        // ==================================================================
        // Merchants
        // ==================================================================

        Route::get(
            '/merchants',
            [SuperAdminMerchantController::class, 'index']
        )->name('merchants.index');


        Route::get(
            '/merchants/create',
            [SuperAdminMerchantController::class, 'create']
        )->name('merchants.create');


        Route::post(
            '/merchants',
            [SuperAdminMerchantController::class, 'store']
        )->name('merchants.store');


        Route::get(
            '/merchants/{encryptedId}/edit',
            [SuperAdminMerchantController::class, 'edit']
        )->name('merchants.edit');


        Route::put(
            '/merchants/{encryptedId}',
            [SuperAdminMerchantController::class, 'update']
        )->name('merchants.update');


        Route::delete(
            '/merchants/{encryptedId}',
            [SuperAdminMerchantController::class, 'destroy']
        )->name('merchants.destroy');


        // ==================================================================
        // Packages
        // ==================================================================

        Route::get(
            '/packages',
            [PackageController::class, 'index']
        )->name('packages.index');


        Route::get(
            '/packages/create',
            [PackageController::class, 'create']
        )->name('packages.create');


        Route::post(
            '/packages',
            [PackageController::class, 'store']
        )->name('packages.store');


        Route::get(
            '/packages/{encryptedId}/edit',
            [PackageController::class, 'edit']
        )->name('packages.edit');


        Route::put(
            '/packages/{encryptedId}',
            [PackageController::class, 'update']
        )->name('packages.update');


        Route::delete(
            '/packages/{encryptedId}',
            [PackageController::class, 'destroy']
        )->name('packages.destroy');


        // ==================================================================
        // Package Durations
        // ==================================================================

        Route::get(
            '/packages/{encryptedId}/durations',
            [PackageDurationController::class, 'index']
        )->name('packages.durations.index');


        Route::get(
            '/packages/{encryptedId}/durations/create',
            [PackageDurationController::class, 'create']
        )->name('packages.durations.create');


        Route::post(
            '/packages/{encryptedId}/durations',
            [PackageDurationController::class, 'store']
        )->name('packages.durations.store');


        Route::get(
            '/packages/{encryptedId}/durations/{duration}/edit',
            [PackageDurationController::class, 'edit']
        )->name('packages.durations.edit');


        Route::put(
            '/packages/{encryptedId}/durations/{duration}',
            [PackageDurationController::class, 'update']
        )->name('packages.update.duration');


        Route::delete(
            '/packages/{encryptedId}/durations/{duration}',
            [PackageDurationController::class, 'destroy']
        )->name('packages.durations.destroy');


        // ==================================================================
        // Subscriptions
        // ==================================================================

        Route::get(
            '/subscriptions',
            [SuperAdminSubscriptionController::class, 'index']
        )->name('subscriptions.index');


        Route::get(
            '/subscriptions/create',
            [SuperAdminSubscriptionController::class, 'create']
        )->name('subscriptions.create');


        Route::post(
            '/subscriptions',
            [SuperAdminSubscriptionController::class, 'store']
        )->name('subscriptions.store');


        Route::get(
            '/subscriptions/{encryptedId}',
            [SuperAdminSubscriptionController::class, 'show']
        )->name('subscriptions.show');


        Route::get(
            '/subscriptions/{encryptedId}/edit',
            [SuperAdminSubscriptionController::class, 'edit']
        )->name('subscriptions.edit');


        Route::put(
            '/subscriptions/{encryptedId}',
            [SuperAdminSubscriptionController::class, 'update']
        )->name('subscriptions.update');


        Route::delete(
            '/subscriptions/{encryptedId}',
            [SuperAdminSubscriptionController::class, 'destroy']
        )->name('subscriptions.destroy');


        // ==================================================================
        // Accounts
        // ==================================================================

        Route::get(
            '/accounts',
            [AccountController::class, 'index']
        )->name('accounts.index');


        Route::get(
            '/accounts/create',
            [AccountController::class, 'create']
        )->name('accounts.create');


        Route::post(
            '/accounts',
            [AccountController::class, 'store']
        )->name('accounts.store');


        Route::get(
            '/accounts/{encryptedId}/edit',
            [AccountController::class, 'edit']
        )->name('accounts.edit');


        Route::put(
            '/accounts/{encryptedId}',
            [AccountController::class, 'update']
        )->name('accounts.update');


        Route::delete(
            '/accounts/{encryptedId}',
            [AccountController::class, 'destroy']
        )->name('accounts.destroy');
    });


// ==========================================================================
// 7. MIDTRANS SUBSCRIPTION NOTIFICATION
// ==========================================================================

Route::post(
    '/midtrans/notification',
    [MidtransNotificationController::class, 'handle']
)->name('midtrans.notification');


// ==========================================================================
// 8. SUBSCRIPTION PAYMENT
// ==========================================================================
//
// PENTING:
// Nama route utama harus:
// public.subscription.payment
//
// Jangan menggunakan:
// public.subscription.payment.show
//
// karena PublicSubscriptionController memanggil:
// route('public.subscription.payment', ...)
//

Route::middleware('auth')
    ->prefix('subscription/payment')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | QRIS STATUS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/qris/{encryptedSubscription}/status',
            [PaymentController::class, 'qrisStatus']
        )->name('public.subscription.payment.qris.status');


        /*
        |--------------------------------------------------------------------------
        | QRIS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/qris/{encryptedSubscription}',
            [PaymentController::class, 'qris']
        )->name('public.subscription.payment.qris');


        /*
        |--------------------------------------------------------------------------
        | SNAP MIDTRANS
        |--------------------------------------------------------------------------
        |
        | INI ROUTE UTAMA PAYMENT
        |
        */

        Route::get(
            '/{encryptedSubscription}',
            [PaymentController::class, 'show']
        )->name('public.subscription.payment');


        /*
        |--------------------------------------------------------------------------
        | Process
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/{encryptedSubscription}',
            [PaymentController::class, 'process']
        )->name('public.subscription.payment.process');
    });


    // EMAIL VERIFICATION
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->name('verification.verify');


// ==========================================================================
// 9. AUTHENTICATED BASIC ROUTES
// ==========================================================================

Route::middleware('auth')->group(function () {


    // ======================================================================
    // Merchant Setup
    // ======================================================================

    Route::get(
        '/merchant/setup',
        [MerchantSetupController::class, 'create']
    )->name('merchant.setup');


    Route::post(
        '/merchant/setup',
        [MerchantSetupController::class, 'store']
    )->name('merchant.setup.store');


    // ======================================================================
    // Email Verification
    // ======================================================================

    Route::get(
        '/verify-email',
        [EmailVerificationController::class, 'show']
    )->name('verification.code');


    Route::post(
        '/verify-email',
        [EmailVerificationController::class, 'verify']
    )->name('verification.code.verify');


    Route::post(
        '/verify-email/resend',
        [EmailVerificationController::class, 'resend']
    )->name('verification.code.resend');
});


// ==========================================================================
// 10. MERCHANT
// ==========================================================================

Route::middleware('auth')
    ->prefix('merchant')
    ->name('merchant.')
    ->group(function () {


        // ==================================================================
        // DASHBOARD
        // ==================================================================

        /*
        |--------------------------------------------------------------------------
        | Dashboard TIDAK menggunakan subscription.active
        |--------------------------------------------------------------------------
        |
        | Tujuannya supaya:
        |
        | Login biasa
        | -> dashboard
        | -> subscription expired
        | -> popup expired langsung muncul
        |
        */

        Route::middleware('role:owner,kasir')
            ->group(function () {

                Route::get(
                    '/dashboard',
                    [DashboardController::class, 'index']
                )->name('dashboard');
            });


        // ==================================================================
        // ORDERS
        // ==================================================================

        Route::middleware([
            'role:owner,kasir,dapur',
            'subscription.active',
        ])->group(function () {


            Route::get(
                '/orders',
                [OrderController::class, 'index']
            )->name('orders.index');


            Route::get(
                '/orders/check',
                [OrderController::class, 'checkNew']
            )->name('orders.check');


            Route::get(
                '/orders/{encryptedId}/receipt',
                [OrderController::class, 'receipt']
            )->name('orders.receipt');


            Route::post(
                '/orders/{encryptedId}/receipt/email',
                [OrderController::class, 'sendReceipt']
            )->name('orders.receipt.email');


            Route::patch(
                '/orders/{encryptedId}/status',
                [OrderController::class, 'updateStatus']
            )->name('orders.status');
        });


        // ==================================================================
        // OWNER
        // ==================================================================

        Route::middleware([
            'role:owner',
            'subscription.active',
        ])->group(function () {


            // ==================================================================
            // QR
            // ==================================================================

            Route::get(
                '/qr',
                [MerchantController::class, 'qrIndex']
            )->name('qr.index');


            Route::post(
                '/qr',
                [MerchantController::class, 'qrStore']
            )->name('qr.store');


            Route::get(
                '/qr/{encryptedId}/print',
                [MerchantController::class, 'qrPrint']
            )->name('qr.print');


            Route::delete(
                '/qr/{encryptedId}',
                [MerchantController::class, 'qrDestroy']
            )->name('qr.destroy');


            // ==================================================================
            // MENU
            // ==================================================================

            Route::get(
                '/menu',
                [MenuController::class, 'index']
            )->name('menu.index');


            Route::post(
                '/category',
                [MenuController::class, 'storeCategory']
            )->name('category.store');


            Route::post(
                '/menu',
                [MenuController::class, 'store']
            )->name('menu.store');


            Route::get(
                '/menu/{encryptedId}/edit',
                [MenuController::class, 'edit']
            )->name('menu.edit');


            Route::put(
                '/menu/{encryptedId}',
                [MenuController::class, 'update']
            )->name('menu.update');


            Route::patch(
                '/menu/{encryptedId}/toggle',
                [MenuController::class, 'toggle']
            )->name('menu.toggle');


            Route::delete(
                '/menu/{encryptedId}',
                [MenuController::class, 'destroy']
            )->name('menu.destroy');


            // ==================================================================
            // PROFILE KAFE
            // ==================================================================

            Route::get(
                '/profile-kafe',
                [MerchantController::class, 'profileEdit']
            )->name('profile-kafe.edit');


            Route::put(
                '/profile-kafe',
                [MerchantController::class, 'profileUpdate']
            )->name('profile-kafe.update');


            // ==================================================================
            // STAFF
            // ==================================================================

            Route::get(
                '/staff',
                [StaffController::class, 'index']
            )->name('staff.index');


            Route::post(
                '/staff',
                [StaffController::class, 'store']
            )->name('staff.store');


            Route::get(
                '/staff/{encryptedId}/edit',
                [StaffController::class, 'edit']
            )->name('staff.edit');


            Route::put(
                '/staff/{encryptedId}',
                [StaffController::class, 'update']
            )->name('staff.update');


            Route::delete(
                '/staff/{encryptedId}',
                [StaffController::class, 'destroy']
            )->name('staff.destroy');
        });
    });


// ==========================================================================
// 11. PROFILE
// ==========================================================================

Route::middleware('auth')->group(function () {


    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');


    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');


    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});


// ==========================================================================
// 12. AUTH
// ==========================================================================

require __DIR__ . '/auth.php';
