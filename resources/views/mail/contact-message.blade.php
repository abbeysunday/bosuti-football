New message from the BOUESTI Sports website

From:    {{ $contactMessage->name }} <{{ $contactMessage->email }}>
@if ($contactMessage->phone)
Phone:   {{ $contactMessage->phone }}
@endif
Subject: {{ $contactMessage->subject }}
Sent:    {{ $contactMessage->created_at->format('d M Y, g:i A') }}

{{ $contactMessage->message }}

---
Reply to this email to answer {{ $contactMessage->name }} directly.
Manage messages: {{ route('admin.contact-messages.show', $contactMessage) }}
