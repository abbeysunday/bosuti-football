<x-layouts.admin title="Seasons">
    <x-admin.page-header title="Seasons" description="Academic football seasons. Exactly one season is marked current.">
        <x-slot name="actions"><a href="{{ route('admin.seasons.create') }}" class="btn btn-primary">New season</a></x-slot>
    </x-admin.page-header>

    @if ($seasons->isEmpty())
        <x-empty-state title="No seasons yet" description="Create the current season before adding competitions and fixtures.">
            <x-slot name="action"><a href="{{ route('admin.seasons.create') }}" class="btn btn-primary">Create season</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Seasons">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Season</th>
                <th scope="col" class="px-4 py-3">Dates</th>
                <th scope="col" class="px-4 py-3">Competitions</th>
                <th scope="col" class="px-4 py-3">Fixtures</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($seasons as $season)
                <tr>
                    <td class="px-4 py-3 font-semibold text-white">{{ $season->name }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $season->start_date?->format('d M Y') ?? '—' }} – {{ $season->end_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $season->competitions_count }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $season->fixtures_count }}</td>
                    <td class="px-4 py-3">
                        @if ($season->is_current)<x-admin.badge color="gold">Current</x-admin.badge>@endif
                        @unless ($season->is_active)<x-admin.badge>Inactive</x-admin.badge>@endunless
                    </td>
                    <td class="px-4 py-2">
                        <x-admin.row-actions :name="$season->name" :edit="route('admin.seasons.edit', $season)" :delete="route('admin.seasons.destroy', $season)" confirm="Seasons with competitions or fixtures cannot be deleted." />
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $seasons->links() }}
    @endif
</x-layouts.admin>
