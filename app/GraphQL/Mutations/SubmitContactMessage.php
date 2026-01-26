<?php

namespace App\GraphQL\Mutations;

use App\Models\ContactMessage;
use App\Mail\ContactMessageNotification;
use App\Mail\ContactAutoReply;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class SubmitContactMessage
{
    /**
     * Submit a new contact message.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        // Rate limiting: 3 submissions per minute per IP
        $key = 'contact-form:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'success' => false,
                'message' => "Too many attempts. Please try again in {$seconds} seconds.",
                'contact' => null,
            ];
        }

        RateLimiter::hit($key, 60);

        // Simple honeypot check (you can add a hidden field in frontend)
        if (request()->has('website') && !empty(request()->input('website'))) {
            // This is likely spam
            Log::warning('Honeypot triggered for contact form', [
                'ip' => request()->ip(),
                'data' => $args,
            ]);

            // Return success to fool the bot
            return [
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon.',
                'contact' => null,
            ];
        }

        try {
            // Store the contact message
            $contactMessage = ContactMessage::create([
                'name' => $args['name'],
                'email' => $args['email'],
                'phone' => $args['phone'],
                'message' => $args['message'],
                'status' => 'new',
                'ip_address' => request()->ip(),
            ]);

            // Send admin notification email (queued)
            $adminEmail = env('ADMIN_EMAIL', 'info@xesstrading.com');
            Mail::to($adminEmail)->queue(new ContactMessageNotification($contactMessage));

            // Send auto-reply to the user (queued)
            Mail::to($args['email'])->queue(new ContactAutoReply($args['name']));

            Log::info('Contact form submitted successfully', [
                'contact_id' => $contactMessage->id,
                'ip' => request()->ip(),
            ]);

            return [
                'success' => true,
                'message' => 'Thank you for contacting us! We will get back to you within 24 hours.',
                'contact' => $contactMessage,
            ];
        } catch (\Exception $e) {
            Log::error('Error submitting contact form', [
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while submitting your message. Please try again later.',
                'contact' => null,
            ];
        }
    }
}
