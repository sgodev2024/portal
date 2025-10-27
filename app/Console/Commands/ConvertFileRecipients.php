<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;
use App\Models\User;

class ConvertFileRecipients extends Command
{
    protected $signature = 'files:convert-recipients';
    protected $description = 'Convert File.recipients from emails to user IDs for existing records';

    public function handle()
    {
        $this->info('Scanning files for recipients that look like emails...');

        $files = File::whereNotNull('recipients')->get();
        $total = $files->count();
        $this->info("Found {$total} files with recipients set");

        $converted = 0;
        foreach ($files as $file) {
            $recipients = $file->recipients;
            if (empty($recipients) || !is_array($recipients)) {
                continue;
            }

            // If first item contains an @, assume these are emails
            $first = $recipients[0] ?? null;
            if ($first && is_string($first) && strpos($first, '@') !== false) {
                $emails = array_unique(array_filter($recipients));
                $users = User::whereIn('email', $emails)->pluck('id')->toArray();

                if (empty($users)) {
                    $this->warn("Skipping file {$file->id}: no matching users for emails");
                    continue;
                }

                $file->recipients = array_values($users);
                $file->save();
                $converted++;
                $this->info("Converted file {$file->id}: emails -> " . count($users) . ' user ids');
            }
        }

        $this->info("Conversion complete. {$converted} files updated.");
        return 0;
    }
}
