<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use HTMLPurifier;
use HTMLPurifier_Config;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('backend.email_templates.index', compact('templates'));
    }


    public function edit(EmailTemplate $email_template)
    {
        return view('backend.email_templates.edit', compact('email_template'));
    }

    public function update(Request $request, EmailTemplate $email_template)
    {
        $request->validate([
            'name'=>'required|string',
            'subject'=>'required|string',
            'body_html'=>'required|string'
        ]);

        $purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
        $cleanHtml = $purifier->purify($request->body_html);

        $email_template->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body_html' => $cleanHtml,
            'from_name' => $request->from_name,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.email_templates.index')->with('success','Template updated');
    }

}
