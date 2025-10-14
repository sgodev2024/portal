<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $newPassword;

    public $tries = 3;
    public $backoff = 20;

    public function __construct(User $user, $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function build()
    {
        $loginLink = route('login');

        return $this->subject('Mật khẩu của bạn đã được đặt lại')
            ->markdown('emails.reset_password', [
                'user' => $this->user,
                'newPassword' => $this->newPassword,
                'login_link' => $loginLink,
            ]);
    }
}
