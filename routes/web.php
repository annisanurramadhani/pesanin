    <?php

    use App\Http\Controllers\CustomerController;
    use App\Http\Controllers\MerchantController;
    use App\Http\Controllers\MenuController;
    use App\Http\Controllers\OrderController;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\StaffController;

    use App\Http\Controllers\PublicSubscription\PublicSubscriptionController;

    use App\Http\Controllers\Superadmin\PackageController;
    use App\Http\Controllers\Superadmin\PackageDurationController;
    use App\Http\Controllers\SuperAdmin\MerchantController as SuperAdminMerchantController;
    use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperAdminSubscriptionController;
    use App\Http\Controllers\SuperAdmin\AccountController;

    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    // 1. Landing Page
    Route::get('/', function () {
        return view('welcome');
    });

    // Public Subscription
    Route::get('/subscription', [PublicSubscriptionController::class, 'index'])
        ->name('public.subscription.index');
    Route::get('/subscription/{slug}', [PublicSubscriptionController::class, 'show'])
        ->name('public.subscription.show');
    Route::get('/subscription/{slug}/summary/{duration}', [PublicSubscriptionController::class, 'summary'])
        ->name('public.subscription.summary');

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
            return redirect()->route('super_admin.dashboard');
        }

        if ($user->role === 'dapur') {
            return redirect()->route('merchant.orders.index');
        }

        return redirect()->route('merchant.dashboard');
    })->middleware(['auth'])->name('dashboard');

    // 4. Special Routes for SUPER ADMIN
    Route::middleware(['auth', 'role:super_admin'])
        ->prefix('super_admin')
        ->name('super_admin.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', function () {
                return view('super_admin.dashboard');
            })->name('dashboard');


            // =====================================================
            // MERCHANTS
            // =====================================================

            Route::get('/merchants', [SuperAdminMerchantController::class, 'index'])
                ->name('merchants.index');

            Route::get('/merchants/create', [SuperAdminMerchantController::class, 'create'])
                ->name('merchants.create');

            Route::post('/merchants', [SuperAdminMerchantController::class, 'store'])
                ->name('merchants.store');

            Route::get('/merchants/{encryptedId}/edit', [SuperAdminMerchantController::class, 'edit'])
                ->name('merchants.edit');

            Route::put('/merchants/{encryptedId}', [SuperAdminMerchantController::class, 'update'])
                ->name('merchants.update');

            Route::delete('/merchants/{encryptedId}', [SuperAdminMerchantController::class, 'destroy'])
                ->name('merchants.destroy');


            // =====================================================
            // PACKAGES
            // =====================================================

            Route::get('/packages', [PackageController::class, 'index'])
                ->name('packages.index');

            Route::get('/packages/create', [PackageController::class, 'create'])
                ->name('packages.create');

            Route::post('/packages', [PackageController::class, 'store'])
                ->name('packages.store');

            Route::get('/packages/{encryptedId}/edit', [PackageController::class, 'edit'])
                ->name('packages.edit');

            Route::put('/packages/{encryptedId}', [PackageController::class, 'update'])
                ->name('packages.update');

            Route::delete('/packages/{encryptedId}', [PackageController::class, 'destroy'])
                ->name('packages.destroy');


            // =====================================================
            // PACKAGE DURATIONS
            // =====================================================

            Route::get('/packages/{encryptedId}/durations', [PackageDurationController::class, 'index'])
                ->name('packages.durations.index');

            Route::get('/packages/{encryptedId}/durations/create', [PackageDurationController::class, 'create'])
                ->name('packages.durations.create');

            Route::post('/packages/{encryptedId}/durations', [PackageDurationController::class, 'store'])
                ->name('packages.durations.store');

            Route::get('/packages/{encryptedId}/durations/{duration}/edit', [PackageDurationController::class, 'edit'])
                ->name('packages.durations.edit');

            Route::put('/packages/{encryptedId}/durations/{duration}', [PackageDurationController::class, 'update'])
                ->name('packages.durations.update');

            Route::delete('/packages/{encryptedId}/durations/{duration}', [PackageDurationController::class, 'destroy'])
                ->name('packages.durations.destroy');

            // =====================================================
            // SUBSCRIPTIONS
            // =====================================================
            Route::get('/subscriptions', [SuperAdminSubscriptionController::class, 'index'])
                ->name('subscriptions.index');

            Route::get('/subscriptions/create', [SuperAdminSubscriptionController::class, 'create'])
                ->name('subscriptions.create');

            Route::post('/subscriptions', [SuperAdminSubscriptionController::class, 'store'])
                ->name('subscriptions.store');

            Route::get('/subscriptions/{encryptedId}', [SuperAdminSubscriptionController::class, 'show'])
                ->name('subscriptions.show');

            Route::get('/subscriptions/{encryptedId}/edit', [SuperAdminSubscriptionController::class, 'edit'])
                ->name('subscriptions.edit');

            Route::put('/subscriptions/{encryptedId}', [SuperAdminSubscriptionController::class, 'update'])
                ->name('subscriptions.update');

            Route::delete('/subscriptions/{encryptedId}', [SuperAdminSubscriptionController::class, 'destroy'])
                ->name('subscriptions.destroy');

            // =====================================================
            // ACCOUNTS
            // =====================================================

            Route::get('/accounts', [AccountController::class, 'index'])
                ->name('accounts.index');

            Route::get('/accounts/create', [AccountController::class, 'create'])
                ->name('accounts.create');

            Route::post('/accounts', [AccountController::class, 'store'])
                ->name('accounts.store');

            Route::get('/accounts/{encryptedId}/edit', [AccountController::class, 'edit'])
                ->name('accounts.edit');

            Route::put('/accounts/{encryptedId}', [AccountController::class, 'update'])
                ->name('accounts.update');

            Route::delete('/accounts/{encryptedId}', [AccountController::class, 'destroy'])
                ->name('accounts.destroy');
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

    require __DIR__ . '/auth.php';
