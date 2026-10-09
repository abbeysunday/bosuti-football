<x-layouts.admin :title="$season->exists ? 'Edit season' : 'New season'">
    <x-admin.page-header :title="$season->exists ? 'Edit ' . $season->name : 'New season'" :back="route('admin.seasons.index')" />

    <form method="POST" action="{{ $season->exists ? route('admin.seasons.update', $season) : route('admin.seasons.store') }}" class="card max-w-3xl p-5 sm:p-8">
        @csrf
        @if ($season->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.field name="name" label="Season name" :value="$season->name" placeholder="2026/2027" required class="sm:col-span-2" />
            <x-admin.field name="start_date" label="Start date" type="date" :value="$season->start_date?->format('Y-m-d')" />
            <x-admin.field name="end_date" label="End date" type="date" :value="$season->end_date?->format('Y-m-d')" />
            <x-admin.field name="is_current" label="Current season" type="checkbox" :value="$season->is_current" help="Marking this season current un-marks the previous one." />
            <x-admin.field name="is_active" label="Active" type="checkbox" :value="$season->is_active" help="Inactive seasons are hidden from public selectors." />
        </div>

        <x-admin.form-actions :cancel="route('admin.seasons.index')" :label="$season->exists ? 'Save changes' : 'Create season'" />
    </form>
</x-layouts.admin>
