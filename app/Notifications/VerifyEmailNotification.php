<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Masa berlaku link verifikasi dalam menit.
     */
    protected int $expireMinutes = 60;

    /**
     * Channel notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Isi email verifikasi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes($this->expireMinutes),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email - PesanIn')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Terima kasih telah membuat akun di PesanIn.')
            ->line('Untuk mengaktifkan akun Anda, silakan verifikasi alamat email ini.')
            ->action('Verifikasi Alamat Email', $verificationUrl)
            ->line('Tautan verifikasi ini berlaku selama 60 menit.')
            ->line('Jika Anda tidak merasa membuat akun PesanIn, Anda dapat mengabaikan email ini.')
            ->salutation("Salam,\nTim PesanIn");
    }
}
