<?php

namespace App\Mail;

use App\Models\Stmt;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $variables;

    public function __construct(EmailTemplate $template, array $variables = [])
    {
        $this->template = $template;
        $this->variables = $variables;
    }

    public function build()
    {
        $stmt = Stmt::first();

        $body = $this->template->body_html;
        foreach ($this->variables as $key => $value) {
            $body = str_replace(['{' . $key . '}', '{{ ' . $key . ' }}'], $value, $body);
        }

        if ($stmt) {
            $fromEmail = $stmt->mail_username;
            $fromName  = $stmt->mail_from_name ?? 'Hệ thống';
        } else {
            $fromEmail = $this->template->from_email ?: config('mail.from.address');
            $fromName  = $this->template->from_name ?: config('mail.from.name');
        }

        Log::info('📧 Email From:', [
            'email' => $fromEmail,
            'name' => $fromName,
            'source' => $stmt ? 'stmt DB' : 'template/config',
            'display' => "$fromName <$fromEmail>"
        ]);

        $mail = $this->from($fromEmail, $fromName)
            ->subject($this->template->subject)
            ->html($body);

        if (!empty($this->variables['attachment_path'])) {
            $attachmentPath = $this->variables['attachment_path'];
            if (str_starts_with($attachmentPath, asset(''))) {
                $attachmentPath = str_replace(asset(''), public_path(''), $attachmentPath);
            }

            if (file_exists($attachmentPath)) {
                $mail->attach(
                    $attachmentPath,
                    ['as' => $this->variables['attachment_name'] ?? basename($attachmentPath)]
                );
            }
        }

        return $mail;
    }
}
