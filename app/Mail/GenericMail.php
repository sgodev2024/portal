<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $variables;

    /**
     * Create a new message instance.
     *
     * @param EmailTemplate $template
     * @param array $variables
     */
    public function __construct(EmailTemplate $template, array $variables = [])
    {
        $this->template = $template;
        $this->variables = $variables;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $body = $this->template->body_html;
        foreach ($this->variables as $key => $value) {
            $body = str_replace('{' . $key . '}', $value, $body);
            $body = str_replace('{{ ' . $key . ' }}', $value, $body);
        }

        $fromEmail = $this->template->from_email ?: config('mail.from.address');
        $fromName  = $this->template->from_name ?: config('mail.from.name');

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
