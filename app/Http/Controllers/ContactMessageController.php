<?php

namespace App\Http\Controllers;

use App\Mail\AdminContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'nullable|string|max:255',
            'first_name'   => 'nullable|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'category'     => 'required|string|in:bug_report,system_issue,partnership,course_inquiry,account_help,billing,general,other',
            'subject'      => 'required|string|max:255',
            'message'      => 'required|string|max:5000',
        ]);

        // Build name from either "name" field or "first_name + last_name"
        $name = $validated['name']
            ?? trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));

        // Set priority based on category
        $formalCategories = ['partnership', 'billing'];
        $priority = in_array($validated['category'], $formalCategories) ? 'high' : 'normal';

        $contactMessage = ContactMessage::create([
            'user_id'      => Auth::id(),
            'name'         => $name,
            'company_name' => $validated['company_name'] ?? null,
            'email'        => $validated['email'],
            'phone'        => $validated['phone'] ?? null,
            'category'     => $validated['category'],
            'priority'     => $priority,
            'subject'      => $validated['subject'],
            'message'      => $validated['message'],
        ]);

        try {
            Mail::to(config('app.admin_notification_email'))->send(new AdminContactMessageReceived($contactMessage));
        } catch (\Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully! We will get back to you soon.',
        ]);
    }
}
