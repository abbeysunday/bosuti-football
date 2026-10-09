<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    /** Saves the message for the admin inbox and, when configured, emails the club. */
    public function store(ContactMessageRequest $request): RedirectResponse
    {
        $contactMessage = ContactMessage::create($request->safe()->except('website'));

        if ($recipient = config('services.contact.notify')) {
            try {
                Mail::to($recipient)->send(new ContactMessageReceived($contactMessage));
            } catch (Throwable $e) {
                // The message is already saved in the admin inbox; a mail outage must not lose it.
                report($e);
            }
        }

        return redirect()->route('contact')
            ->with('contact_sent', 'Thank you, ' . $contactMessage->name . '. Your message has been sent to the football team and we will reply by email.');
    }
}
