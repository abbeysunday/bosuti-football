@props(['status'])

@php
    $color = ['scheduled' => 'blue', 'live' => 'red', 'completed' => 'green', 'postponed' => 'gold', 'cancelled' => 'gray',
              'pending' => 'gold', 'reviewing' => 'blue', 'accepted' => 'green', 'rejected' => 'red'][$status] ?? 'gray';
    $label = \App\Models\Fixture::STATUSES[$status] ?? \App\Models\TrialApplication::STATUSES[$status] ?? ucfirst($status);
@endphp

<x-admin.badge :color="$color">
    @if ($status === 'live')<span class="h-1.5 w-1.5 animate-pulse rounded-full bg-current" aria-hidden="true"></span>@endif
    {{ $label }}
</x-admin.badge>
