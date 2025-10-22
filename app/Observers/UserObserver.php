<?php

namespace App\Observers;
use App\Models\User;
use App\Models\UserStorageQuota;
use App\Models\UserFolder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Tự động tạo thư mục và storage quota khi tạo user
     */
    public function created(User $user)
    {
        try {
            // 1. Tạo thư mục vật lý trong storage
            $folderPath = 'users/user_' . $user->id;
            Storage::disk('public')->makeDirectory($folderPath);

            // 2. Tạo storage quota (1GB mặc định)
            UserStorageQuota::create([
                'user_id' => $user->id,
                'quota_limit' => 1073741824, // 1GB = 1024 * 1024 * 1024 bytes
                'used_space' => 0,
                'last_calculated_at' => now(),
            ]);

            Log::info("Created folder and quota for user: {$user->id}");

        } catch (\Exception $e) {
            Log::error("Failed to create folder for user {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "deleting" event.
     * Xóa tất cả file và folder của user khi xóa user
     */
    public function deleting(User $user)
    {
        try {
            // 1. Xóa tất cả folders trong database
            UserFolder::where('user_id', $user->id)->delete();

            // 2. Xóa tất cả files trong database
            \App\Models\UserFile::where('user_id', $user->id)->delete();

            // 3. Xóa file activities
            \App\Models\UserFileActivity::where('user_id', $user->id)->delete();

            // 4. Xóa storage quota
            UserStorageQuota::where('user_id', $user->id)->delete();

            // 5. Xóa thư mục vật lý trong storage
            $folderPath = 'users/user_' . $user->id;
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            Log::info("Deleted all files and folders for user: {$user->id}");

        } catch (\Exception $e) {
            Log::error("Failed to delete files for user {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user)
    {
        // Tái tạo folder nếu user được khôi phục
        $this->created($user);
    }
}