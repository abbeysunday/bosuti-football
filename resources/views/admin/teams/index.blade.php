<x-layouts.admin title="Teams">
    <x-admin.page-header title="Teams" description="Internal BOUESTI student football clubs.">
        <x-slot name="actions"><a href="{{ route('admin.teams.create') }}" class="btn btn-primary">New team</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.teams.index')" placeholder="Search teams" />

    @if ($teams->isEmpty())
        <x-empty-state title="No teams found" description="{{ request('q') ? 'No team matches your search.' : 'Add the first BOUESTI team.' }}">
            <x-slot name="action"><a href="{{ route('admin.teams.create') }}" class="btn btn-primary">Create team</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Teams">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Team</th>
                <th scope="col" class="px-4 py-3">Short name</th>
                <th scope="col" class="px-4 py-3">Players</th>
                <th scope="col" class="px-4 py-3">Coach</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($teams as $team)
                <tr>
                    <td class="px-4 py-3"><a href="{{ route('admin.teams.show', $team) }}" class="inline-flex min-h-[44px] items-center rounded hover:underline"><x-admin.team :team="$team" /></a></td>
                    <td class="px-4 py-3 text-ink-2">{{ $team->short_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $team->players_count }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $team->coach_name ?? '—' }}</td>
                    <td class="px-4 py-3"><x-admin.badge :color="$team->is_active ? 'green' : 'gray'">{{ $team->is_active ? 'Active' : 'Inactive' }}</x-admin.badge></td>
                    <td class="px-4 py-2">
                        <x-admin.row-actions :name="$team->name" :show="route('admin.teams.show', $team)" :edit="route('admin.teams.edit', $team)" :delete="route('admin.teams.destroy', $team)"
                            confirm="The team and its players are archived. Past fixtures and results are kept." />
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $teams->links() }}
    @endif
</x-layouts.admin>
