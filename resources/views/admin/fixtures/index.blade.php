@php $isResults = request('status') === 'completed'; @endphp

<x-layouts.admin :title="$isResults ? 'Results' : 'Fixtures'">
    <x-admin.page-header :title="$isResults ? 'Results' : 'Fixtures'" :description="$isResults ? 'Completed matches. Open one to edit the score, events or line-ups.' : 'Schedule matches between BOUESTI teams and record results.'">
        <x-slot name="actions"><a href="{{ route('admin.fixtures.create') }}" class="btn btn-primary">New fixture</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.fixtures.index')" :search="false">
        <x-admin.select-filter name="competition" label="Competition" :options="$competitions" all="All competitions" />
        <x-admin.select-filter name="team" label="Team" :options="$teams" all="All teams" />
        <x-admin.select-filter name="status" label="Status" :options="\App\Models\Fixture::STATUSES" all="All statuses" />
    </x-admin.filters>

    @if ($fixtures->isEmpty())
        <x-empty-state title="{{ $isResults ? 'No results yet' : 'No fixtures found' }}" description="{{ $isResults ? 'Enter a final score on a fixture to see it here.' : 'Create a fixture such as Amapro FC vs Elite FC.' }}">
            <x-slot name="action"><a href="{{ route('admin.fixtures.create') }}" class="btn btn-primary">Create fixture</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Fixtures">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Date</th>
                <th scope="col" class="px-4 py-3 text-end">Home</th>
                <th scope="col" class="px-4 py-3 text-center">Score</th>
                <th scope="col" class="px-4 py-3">Away</th>
                <th scope="col" class="px-4 py-3">Competition</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($fixtures as $fixture)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3">
                        <span class="block font-semibold text-white">{{ $fixture->match_date->format('D d M Y') }}</span>
                        <span class="text-xs text-ink-muted">{{ $fixture->kickoff_label ?? 'Time TBC' }}{{ $fixture->matchday ? ' · MD ' . $fixture->matchday : '' }}</span>
                    </td>
                    <td class="px-4 py-3 text-end"><x-admin.team :team="$fixture->homeTeam" reverse /></td>
                    <td class="px-4 py-3 text-center font-display text-xl font-extrabold {{ $fixture->hasScore() ? 'text-gold-light' : 'text-ink-muted' }}">{{ $fixture->scoreline }}</td>
                    <td class="px-4 py-3"><x-admin.team :team="$fixture->awayTeam" /></td>
                    <td class="px-4 py-3 text-ink-2">{{ $fixture->competition?->display_name }}</td>
                    <td class="px-4 py-3"><x-admin.status-badge :status="$fixture->status" /></td>
                    <td class="px-4 py-2">
                        <x-admin.row-actions :name="$fixture->title" :show="route('admin.fixtures.show', $fixture)" :edit="route('admin.fixtures.edit', $fixture)" :delete="route('admin.fixtures.destroy', $fixture)" confirm="Its result, match events and line-ups are deleted too." />
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $fixtures->links() }}
    @endif
</x-layouts.admin>
