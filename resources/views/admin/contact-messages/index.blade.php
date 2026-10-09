<x-layouts.admin title="Messages">
    <x-admin.page-header title="Messages" description="Messages sent through the website's Contact page." />

    <div class="mb-5 flex gap-2 overflow-x-auto pb-1" role="group" aria-label="Filter by status">
        @foreach (['' => 'All'] + \App\Models\ContactMessage::STATUSES as $key => $label)
            @php $active = (string) request('status') === (string) $key; @endphp
            <a href="{{ route('admin.contact-messages.index', array_filter(['status' => $key, 'q' => request('q')])) }}"
               @class(['btn shrink-0', 'btn-primary' => $active, 'btn-outline' => ! $active]) @if ($active) aria-current="page" @endif>
                {{ $label }}
                <span class="opacity-70">{{ $key === '' ? $counts->sum() : ($counts[$key] ?? 0) }}</span>
            </a>
        @endforeach
    </div>

    <x-admin.filters :action="route('admin.contact-messages.index')" placeholder="Name, email or subject">
        @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    </x-admin.filters>

    @if ($messages->isEmpty())
        <x-empty-state title="No messages" description="Messages from the Contact page will appear here." />
    @else
        <x-admin.table label="Messages">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">From</th>
                <th scope="col" class="px-4 py-3">Subject</th>
                <th scope="col" class="px-4 py-3">Received</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($messages as $message)
                <tr @class(['font-semibold' => $message->status === 'new'])>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="inline-flex min-h-[44px] flex-col justify-center text-white hover:underline">
                            {{ $message->name }}
                            <span class="text-xs font-normal text-ink-muted">{{ $message->email }}</span>
                        </a>
                    </td>
                    <td class="max-w-xs px-4 py-3 text-ink-2"><span class="line-clamp-2">{{ $message->subject }}</span></td>
                    <td class="whitespace-nowrap px-4 py-3 text-ink-2">{{ $message->created_at->format('d M Y, g:i A') }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :color="['new' => 'gold', 'read' => 'blue', 'replied' => 'green', 'archived' => 'gray'][$message->status] ?? 'gray'">{{ $message->status_label }}</x-admin.badge>
                    </td>
                    <td class="px-4 py-2"><x-admin.row-actions :name="'message from ' . $message->name" :show="route('admin.contact-messages.show', $message)" :delete="route('admin.contact-messages.destroy', $message)" /></td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $messages->links() }}
    @endif
</x-layouts.admin>
