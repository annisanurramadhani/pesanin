<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionInvoiceNotification extends Notification
{
    use Queueable;

    protected Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Channel yang digunakan.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Isi email invoice.
     */
    public function toMail(object $notifiable): MailMessage
    {
         $setting = WebsiteSetting::first();

        return (new MailMessage)
            ->subject(
                'Invoice Langganan ' . $setting->website_name . ' - ' .
                $this->subscription->invoice_number
            )
            ->view(
                'emails.subscription-invoice',
                [
                    'user' => $notifiable,
                    'subscription' => $this->subscription, 'setting' => $setting,
                ]
            );
    }
}
