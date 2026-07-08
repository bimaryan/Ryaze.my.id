<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from('resetpassword@ryaze.my.id', 'Ryaze Security')
            ->subject('Permintaan Reset Password')
            ->view('emails.custom-auth', [
                'title' => 'Reset Password',
                'intro' => 'Kami menerima permintaan untuk mereset password akun Anda di Ryaze Portal. Silakan klik tombol di bawah ini untuk membuat password baru.',
                'actionText' => 'Reset Password Sekarang',
                'actionUrl' => url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)),
                'outro' => 'Jika Anda tidak pernah meminta reset password, abaikan saja email ini. Tautan ini akan kedaluwarsa dalam ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' menit.'
            ]);
    }
}
