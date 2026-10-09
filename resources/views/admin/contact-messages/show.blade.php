<x-layouts.admin :title="$message->subject">
    <x-admin.page-header :title="$message->subject" :description="'From ' . $message->name . ' · ' . $message->created_at->format('d M Y, g:i A')" :back="route('admin.contact-messages.index')">
        <x-slot name="actions">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . $message->subject) }}" class="btn btn-primary">Reply by email</a>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-[1fr_24rem]">
        <x-admin.section title="Message">
            <dl class="mb-6 grid gap-x-6 gap-y-4 sm:grid-cols-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-muted">Name</dt>
                    <dd class="mt-1 break-words text-white">{{ $message->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-muted">Email</dt>
                    <dd class="mt-1 break-words"><a href="mailto:{{ $message->email }}" class="link">{{ $message->email }}</a></dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-muted">Phone</dt>
                    <dd class="mt-1 text-white">
                        @if ($message->phone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $message->phone) }}" class="link">{{ $message->phone }}</a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
            <div class="whitespace-pre-line rounded-xl border border-subtle bg-pitch-900/60 p-4 leading-relaxed text-ink">{{ $message->message }}</div>
        </x-admin.section>

        <div class="space-y-6">
            <x-admin.section title="Status">
                <form method="POST" action="{{ route('admin.contact-messages.update', $message) }}" class="grid gap-4">
                    @csrf
                    @method('PUT')
                    <x-admin.field name="status" label="Status" type="select" :options="\App\Models\ContactMessage::STATUSES" :value="$message->status" help="Mark as Replied once you have answered by email." />
                    <x-admin.field name="admin_notes" label="Internal notes" type="textarea" rows="4" :value="$message->admin_notes" help="Only visible to admins." />
                    <button type="submit" class="btn btn-primary" data-loading-text="Saving…">Save</button>
                </form>
            </x-admin.section>

            <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" data-confirm="Delete this message from {{ $message->name }}? This cannot be undone.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-ghost w-full text-danger hover:bg-danger/10 hover:text-danger">Delete message</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
