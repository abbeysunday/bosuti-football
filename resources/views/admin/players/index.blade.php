<x-layouts.admin title="Players">
    <x-admin.page-header title="Players" description="Student players across all BOUESTI teams. Matric numbers are visible to admins only.">
        <x-slot name="actions"><a href="{{ route('admin.players.create', ['team' => request('team')]) }}" class="btn btn-primary">Add player</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.players.index')" placeholder="Name or matric number">
        <x-admin.select-filter name="team" label="Team" :options="$teams" all="All teams" />
        <x-admin.select-filter name="position" label="Position" :options="\App\Models\Player::POSITIONS" all="All positions" />
    </x-admin.filters>

    @if ($players->isEmpty())
        <x-empty-state title="No players found" description="{{ request()->hasAny(['q', 'team', 'position']) ? 'Try a different search or filter.' : 'Add students to their teams to build each squad.' }}">
            <x-slot name="action"><a href="{{ route('admin.players.create', ['team' => request('team')]) }}" class="btn btn-primary">Add player</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Players">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Player</th>
                <th scope="col" class="px-4 py-3">#</th>
                <th scope="col" class="px-4 py-3">Team</th>
                <th scope="col" class="px-4 py-3">Position</th>
                <th scope="col" class="px-4 py-3">Matric no.</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($players as $player)
                <tr>
                    <td class="px-4 py-3">
                        <span class="flex items-center gap-3">
                            @if ($player->photo_url)
                                <img src="{{ $player->photo_url }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover">
                            @else
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-pitch-800 text-xs font-bold text-ink-2" aria-hidden="true">{{ mb_substr($player->first_name, 0, 1) }}{{ mb_substr($player->last_name, 0, 1) }}</span>
                            @endif
                            <span class="font-semibold text-white">{{ $player->full_name }}
                                @if ($player->is_captain)<x-admin.badge color="gold" class="ms-1">C</x-admin.badge>@endif
                                @if ($player->is_featured)<x-admin.badge color="blue" class="ms-1">Featured</x-admin.badge>@endif
                            </span>
                        </span>
                    </td>
                    <td class="px-4 py-3 font-display text-lg font-extrabold text-gold-light">{{ $player->jersey_number ?? '—' }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $player->team?->name }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $player->position_label }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-ink-muted">{{ $player->matric_number ?? '—' }}</td>
                    <td class="px-4 py-3"><x-admin.badge :color="$player->is_active ? 'green' : 'gray'">{{ $player->is_active ? 'Active' : 'Inactive' }}</x-admin.badge></td>
                    <td class="px-4 py-2">
                        <x-admin.row-actions :name="$player->full_name" :show="route('players.show', $player)" :edit="route('admin.players.edit', $player)" :delete="route('admin.players.destroy', $player)" confirm="Their goals, cards and line-ups in past matches are kept." />
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $players->links() }}
    @endif
</x-layouts.admin>
