<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class CustomVerifyEmail extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return (new MailMessage)
            ->from('verifikasi@ryaze.my.id', 'Ryaze Security')
            ->subject('Verifikasi Alamat Email Anda')
            ->view('emails.custom-auth', [
                'title' => 'Verifikasi Email',
                'intro' => 'Terima kasih telah mendaftar di Ryaze Portal. Untuk mulai menggunakan semua fitur kami, silakan verifikasi alamat email Anda dengan mengeklik tombol di bawah ini.',
                'actionText' => 'Verifikasi Email Sekarang',
                'actionUrl' => $verificationUrl,
                'outro' => 'Jika Anda tidak merasa membuat akun ini, abaikan saja email ini. Tautan ini akan kedaluwarsa dalam 60 menit.'
            ]);
    }
}
