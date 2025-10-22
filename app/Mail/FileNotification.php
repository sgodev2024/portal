<?php

namespace App\Mail;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FileNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $file;
    public $downloadUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(File $file, $downloadUrl)
    {
        $this->file = $file;
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Báo cáo mới: {$this->file->title}")
                    ->view('emails.file_notification')
                    ->with([
                        'file' => $this->file,
                        'downloadUrl' => $this->downloadUrl,
                    ]);
    }
}

