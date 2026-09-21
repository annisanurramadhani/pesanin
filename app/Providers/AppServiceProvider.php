<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Models\WalletTransaction;
use App\Observers\WalletTransactionObserver;
use App\Models\Withdrawal;
use App\Observers\WithdrawalObserver;
use App\Models\Menu;
use App\Observers\MenuObserver;
use App\Models\User;
use App\Observers\UserObserver;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Models\Category;
use App\Observers\CategoryObserver;
use App\Models\Merchant;
use App\Observers\MerchantObserver;
use App\Models\Subscription;
use App\Observers\SubscriptionObserver;
use App\Models\MerchantBankAccount;
use App\Observers\MerchantBankAccountObserver;
use App\Models\QrCode;
use App\Observers\QrCodeObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);

        WalletTransaction::observe(
            WalletTransactionObserver::class
        );

        Withdrawal::observe(
            WithdrawalObserver::class
        );

        Menu::observe(
            MenuObserver::class
        );

        User::observe(
            UserObserver::class
        );

        Event::listen(
            Login::class,
            LogSuccessfulLogin::class
        );

        Event::listen(
            Logout::class,
            LogSuccessfulLogout::class
        );

        Category::observe(
            CategoryObserver::class
        );

        Merchant::observe(
            MerchantObserver::class
        );

        Subscription::observe(
            SubscriptionObserver::class
        );

        MerchantBankAccount::observe(
            MerchantBankAccountObserver::class
        );

        QrCode::observe(
            QrCodeObserver::class
        );
    }
}
