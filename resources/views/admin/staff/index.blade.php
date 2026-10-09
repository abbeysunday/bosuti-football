<x-layouts.admin title="Management & staff">
    <x-admin.page-header title="Management & staff" description="People shown on the Management and Coaching Staff pages.">
        <x-slot name="actions"><a href="{{ route('admin.staff.create') }}" class="btn btn-primary">Add staff member</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.staff.index')" placeholder="Search names">
        <x-admin.select-filter name="type" label="Type" :options="\App\Models\Staff::TYPES" all="All types" />
    </x-admin.filters>

    @if ($staff->isEmpty())
        <x-empty-state title="No staff added" description="Add verified management and coaching staff with their official roles.">
            <x-slot name="action"><a href="{{ route('admin.staff.create') }}" class="btn btn-primary">Add staff member</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Staff">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Name</th>
                <th scope="col" class="px-4 py-3">Role</th>
                <th scope="col" class="px-4 py-3">Type</th>
                <th scope="col" class="px-4 py-3">Team</th>
                <th scope="col" class="px-4 py-3">Order</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($staff as $member)
                <tr>
                    <td class="px-4 py-3">
                        <span class="flex items-center gap-3">
                            @if ($member->photo_url)<img src="{{ $member->photo_url }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover">@endif
                            <span class="font-semibold text-white">{{ $member->name }}@unless ($member->is_active)<x-admin.badge class="ms-1">Hidden</x-admin.badge>@endunless</span>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-ink-2">{{ $member->role }}</td>
                    <td class="px-4 py-3"><x-admin.badge :color="$member->type === 'coaching' ? 'green' : 'blue'">{{ $member->type_label }}</x-admin.badge></td>
                    <td class="px-4 py-3 text-ink-2">{{ $member->team?->name ?? 'All teams' }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $member->sort_order }}</td>
                    <td class="px-4 py-2"><x-admin.row-actions :name="$member->name" :edit="route('admin.staff.edit', $member)" :delete="route('admin.staff.destroy', $member)" /></td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $staff->links() }}
    @endif
</x-layouts.admin>
