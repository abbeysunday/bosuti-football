<x-layouts.admin title="Trial applications">
    <x-admin.page-header title="Trial applications" description="Students who applied through the Join the Team page." />

    <div class="mb-5 flex gap-2 overflow-x-auto pb-1" role="group" aria-label="Filter by status">
        @foreach (['' => 'All'] + \App\Models\TrialApplication::STATUSES as $key => $label)
            @php $active = (string) request('status') === (string) $key; @endphp
            <a href="{{ route('admin.trial-applications.index', array_filter(['status' => $key, 'q' => request('q')])) }}"
               @class(['btn shrink-0', 'btn-primary' => $active, 'btn-outline' => ! $active]) @if ($active) aria-current="page" @endif>
                {{ $label }}
                <span class="opacity-70">{{ $key === '' ? $counts->sum() : ($counts[$key] ?? 0) }}</span>
            </a>
        @endforeach
    </div>

    <x-admin.filters :action="route('admin.trial-applications.index')" placeholder="Name, email or matric number">
        @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    </x-admin.filters>

    @if ($applications->isEmpty())
        <x-empty-state title="No applications" description="New applications from the trials form will appear here." />
    @else
        <x-admin.table label="Trial applications">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Applicant</th>
                <th scope="col" class="px-4 py-3">Position</th>
                <th scope="col" class="px-4 py-3">Level</th>
                <th scope="col" class="px-4 py-3">Received</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($applications as $application)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.trial-applications.show', $application) }}" class="block font-semibold text-white hover:underline">{{ $application->full_name }}</a>
                        <span class="text-xs text-ink-muted">{{ $application->department ?? 'Department not given' }}</span>
                    </td>
                    <td class="px-4 py-3 text-ink-2">{{ $application->position_label ?? '—' }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ $application->level ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-ink-2">{{ $application->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3"><x-admin.status-badge :status="$application->status" /></td>
                    <td class="px-4 py-2"><x-admin.row-actions :name="$application->full_name . '’s application'" :show="route('admin.trial-applications.show', $application)" :delete="route('admin.trial-applications.destroy', $application)" /></td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $applications->links() }}
    @endif
</x-layouts.admin>
