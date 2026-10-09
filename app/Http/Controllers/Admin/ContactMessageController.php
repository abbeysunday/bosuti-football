<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        return view('admin.contact-messages.index', [
            'messages' => ContactMessage::status($request->input('status'))
                ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'counts' => ContactMessage::selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        // Opening a new message marks it as read.
        if ($contactMessage->status === 'new') {
            $contactMessage->forceFill(['status' => 'read'])->save();
        }

        return view('admin.contact-messages.show', ['message' => $contactMessage]);
    }

    public function update(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ContactMessage::STATUSES))],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $contactMessage->forceFill($data)->save();

        return redirect()->route('admin.contact-messages.show', $contactMessage)
            ->with('success', "Message marked as {$contactMessage->status_label}.");
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted.');
    }
}
