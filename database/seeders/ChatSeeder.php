<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Chat;
use Carbon\Carbon;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('role', 2)->first();
        $customer = User::where('role', 3)->first();

        if (!$staff || !$customer) {
            $this->command->info('Staff hoặc Customer chưa tồn tại. Chạy UserSeeder trước.');
            return;
        }

        // Tạo chat với content JSON
        $chat = Chat::create([
            'user_id' => $customer->id,
            'staff_id' => $staff->id,
            'status' => 'processing',
            'last_message_at' => Carbon::now(),
            'content' => [
                [
                    'sender_id' => $customer->id,
                    'type' => 'text',
                    'message' => 'Xin chào, tôi cần hỗ trợ.',
                    'file_path' => null,
                    'file_name' => null,
                    'created_at' => Carbon::now()->toDateTimeString(),
                ],
                [
                    'sender_id' => $staff->id,
                    'type' => 'text',
                    'message' => 'Chào bạn, tôi có thể giúp gì?',
                    'file_path' => null,
                    'file_name' => null,
                    'created_at' => Carbon::now()->toDateTimeString(),
                ],
            ],
        ]);
    }
}
