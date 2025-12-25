<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     */
    public function index()
    {
        return Inertia::render('Contact/Index', [
            'recaptchaSiteKey' => config('services.recaptcha.site_key'),
        ]);
    }

    /**
     * Store a contact form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'recaptcha_token' => 'required|string',
        ]);

        // Verify reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $recaptchaSecret,
            'response' => $validated['recaptcha_token'],
            'remoteip' => $request->ip(),
        ]);

        $recaptchaData = $recaptchaResponse->json();

        if (!$recaptchaData['success'] || $recaptchaData['score'] < 0.5) {
            return back()->with('error', 'reCAPTCHA verification failed. Please try again.');
        }

        // Create contact message
        $contactMessage = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        // Send email notification to all admin users
        $admins = User::all();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new ContactFormSubmitted($contactMessage));
        }

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
