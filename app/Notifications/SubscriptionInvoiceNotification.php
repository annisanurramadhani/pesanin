<?php

namespace App\Notifications;

use App\Models\Subscription;
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
        return (new MailMessage)
            ->subject(
                'Invoice Langganan PesanIn - ' .
                $this->subscription->invoice_number
            )
            ->view(
                'emails.subscription-invoice',
                [
                    'user' => $notifiable,
                    'subscription' => $this->subscription,
                ]
            );
    }
}
