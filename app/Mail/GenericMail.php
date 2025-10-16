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
     * @param array $variables - Mảng biến để replace: ['user_name' => 'Nguyen Van A', 'new_password' => '123456', ...]
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
        $fromName = $this->template->from_name ?: config('mail.from.name');

        return $this
            ->from($fromName)
            ->subject($this->template->subject)
            ->html($body);
    }
}
