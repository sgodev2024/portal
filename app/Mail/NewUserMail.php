<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $default_password;

    /**
     * Số lần thử gửi lại nếu bị lỗi queue.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Thời gian chờ (giây) giữa các lần thử lại.
     *
     * @var int|array
     */
    public $backoff = 30;

    /**
     * Tạo instance cho mail.
     */
    public function __construct(User $user, $default_password)
    {
        $this->user = $user;
        $this->default_password = $default_password;
    }

    /**
     * Xây dựng nội dung email.
     */
    public function build()
    {
        $loginLink = route('login');

        return $this->subject('Tài khoản của bạn đã được tạo - Vui lòng cập nhật thông tin')
            ->markdown('emails.new_user', [
                'user' => $this->user,
                'default_password' => $this->default_password,
                'login_link' => $loginLink,
            ]);
    }
}
