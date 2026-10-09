<x-layouts.admin :title="$application->full_name">
    <x-admin.page-header :title="$application->full_name" :description="'Applied ' . $application->created_at->format('d M Y, g:i A')" :back="route('admin.trial-applications.index')" />

    <div class="grid gap-6 xl:grid-cols-[1fr_24rem]">
        <x-admin.section title="Application">
            <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                @foreach ([
                    'Email' => $application->email,
                    'Phone' => $application->phone,
                    'Matric number' => $application->matric_number,
                    'Department' => $application->department,
                    'Level' => $application->level,
                    'Preferred position' => $application->position_label,
                    'Dominant foot' => $application->dominant_foot ? ucfirst($application->dominant_foot) : null,
                    'Height' => $application->height,
                    'Previous team' => $application->previous_team,
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $label }}</dt>
                        <dd class="mt-1 break-words text-white">
                            @if ($label === 'Email' && $value)
                                <a href="mailto:{{ $value }}" class="link">{{ $value }}</a>
                            @elseif ($label === 'Phone' && $value)
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $value) }}" class="link">{{ $value }}</a>
                            @else
                                {{ $value ?? '—' }}
                            @endif
                        </dd>
                    </div>
                @endforeach
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-muted">Playing experience</dt>
                    <dd class="mt-1 whitespace-pre-line text-ink-2">{{ $application->playing_experience ?? '—' }}</dd>
                </div>
            </dl>
        </x-admin.section>

        <div class="space-y-6">
            <x-admin.section title="Decision">
                <form method="POST" action="{{ route('admin.trial-applications.update', $application) }}" class="grid gap-4">
                    @csrf
                    @method('PUT')
                    <x-admin.field name="status" label="Status" type="select" :options="\App\Models\TrialApplication::STATUSES" :value="$application->status" />
                    <x-admin.field name="admin_notes" label="Internal notes" type="textarea" rows="4" :value="$application->admin_notes" help="Only visible to admins." />
                    <button type="submit" class="btn btn-primary" data-loading-text="Saving…">Save decision</button>
                </form>
            </x-admin.section>

            @if ($application->status === 'accepted')
                <x-alert type="success" title="Accepted">
                    Add this student to a team: <a href="{{ route('admin.players.create') }}" class="link">create player</a>.
                </x-alert>
            @endif

            <form method="POST" action="{{ route('admin.trial-applications.destroy', $application) }}" data-confirm="Delete {{ $application->full_name }}’s application? This cannot be undone.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-ghost w-full text-danger hover:bg-danger/10 hover:text-danger">Delete application</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
