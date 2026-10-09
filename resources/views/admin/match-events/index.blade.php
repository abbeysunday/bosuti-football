<x-layouts.admin title="Match events">
    <x-admin.page-header title="Match events" description="Every goal, card and substitution recorded. Events are added from each fixture's page." />

    <x-admin.filters :action="route('admin.match-events.index')" :search="false">
        <x-admin.select-filter name="type" label="Event type" :options="\App\Models\MatchEvent::TYPES" all="All events" />
    </x-admin.filters>

    @if ($events->isEmpty())
        <x-empty-state title="No match events yet" description="Open a fixture to add goals, assists, cards and substitutions.">
            <x-slot name="action"><a href="{{ route('admin.fixtures.index') }}" class="btn btn-primary">Go to fixtures</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Match events">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Match</th>
                <th scope="col" class="px-4 py-3">Min</th>
                <th scope="col" class="px-4 py-3">Event</th>
                <th scope="col" class="px-4 py-3">Player</th>
                <th scope="col" class="px-4 py-3">Team</th>
            </x-slot>
            @foreach ($events as $event)
                <tr>
                    <td class="px-4 py-3"><a href="{{ route('admin.fixtures.show', $event->fixture) }}#events" class="link">{{ $event->fixture->title }}</a></td>
                    <td class="px-4 py-3 font-display text-lg font-extrabold text-gold-light">{{ $event->minute_label }}</td>
                    <td class="px-4 py-3"><x-admin.badge :color="in_array($event->type, ['goal', 'penalty_scored']) ? 'green' : ($event->type === 'red_card' ? 'red' : ($event->type === 'yellow_card' ? 'gold' : 'gray'))">{{ $event->type_label }}</x-admin.badge></td>
                    <td class="px-4 py-3 text-white">{{ $event->player?->full_name ?? '—' }}@if ($event->relatedPlayer)<span class="block text-xs text-ink-muted">{{ $event->related_label }}: {{ $event->relatedPlayer->full_name }}</span>@endif</td>
                    <td class="px-4 py-3 text-ink-2">{{ $event->team?->name }}</td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $events->links() }}
    @endif
</x-layouts.admin>
