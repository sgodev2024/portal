<?php

namespace App\Providers;

use App\Models\Stmt;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        User::observe(UserObserver::class);

        // --- Cấu hình MAIL động từ DB (bảng stmt) ---
        try {
            if (Schema::hasTable('stmt')) { // tránh lỗi khi migrate
                $stmt = Stmt::first();
                if ($stmt) {
                    Config::set('mail.mailers.smtp', [
                        'transport' => 'smtp',
                        'host' => 'smtp.gmail.com',
                        'port' => 587,
                        'encryption' => 'tls',
                        'username' => $stmt->mail_username,
                        'password' => $stmt->mail_password,
                    ]);

                    Config::set('mail.from', [
                        'address' => $stmt->mail_username,
                        'name' => $stmt->mail_from_name ?? 'Hệ thống',
                    ]);
                }
            }
        } catch (\Exception $e) {
            // tránh crash khi chưa có DB hoặc lỗi kết nối
            Log::warning('Không thể load cấu hình mail từ DB: ' . $e->getMessage());
        }
    }
}
