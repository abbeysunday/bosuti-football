<x-layouts.admin title="Competitions">
    <x-admin.page-header title="Competitions" description="Leagues, cups and tournaments between BOUESTI teams.">
        <x-slot name="actions"><a href="{{ route('admin.competitions.create') }}" class="btn btn-primary">New competition</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.competitions.index')" placeholder="Search competitions">
        <x-admin.select-filter name="season" label="Season" :options="$seasons" all="All seasons" />
    </x-admin.filters>

    @if ($competitions->isEmpty())
        <x-empty-state title="No competitions found" description="Create a league or cup to start scheduling fixtures.">
            <x-slot name="action"><a href="{{ route('admin.competitions.create') }}" class="btn btn-primary">Create competition</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Competitions">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Competition</th>
                <th scope="col" class="px-4 py-3">Season</th>
                <th scope="col" class="px-4 py-3">Type</th>
                <th scope="col" class="px-4 py-3">Fixtures</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($competitions as $competition)
                <tr>
                    <td class="px-4 py-3">
                        <span class="block font-semibold text-white">{{ $competition->name }}</span>
                        @if ($competition->short_name)<span class="text-xs text-ink-muted">{{ $competition->short_name }}</span>@endif
                    </td>
                    <td class="px-4 py-3 text-ink-2">{{ $competition->season?->name }}</td>
                    <td class="px-4 py-3"><x-admin.badge color="blue">{{ $competition->type_label }}</x-admin.badge></td>
                    <td class="px-4 py-3 text-ink-2"><a class="link" href="{{ route('admin.fixtures.index', ['competition' => $competition->id]) }}">{{ $competition->fixtures_count }}</a></td>
                    <td class="px-4 py-3"><x-admin.badge :color="$competition->is_active ? 'green' : 'gray'">{{ $competition->is_active ? 'Active' : 'Inactive' }}</x-admin.badge></td>
                    <td class="px-4 py-2">
                        <x-admin.row-actions :name="$competition->name" :edit="route('admin.competitions.edit', $competition)" :delete="route('admin.competitions.destroy', $competition)" confirm="Competitions with fixtures cannot be deleted." />
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $competitions->links() }}
    @endif
</x-layouts.admin>
