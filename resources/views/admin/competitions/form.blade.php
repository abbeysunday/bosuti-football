<x-layouts.admin :title="$competition->exists ? 'Edit competition' : 'New competition'">
    <x-admin.page-header :title="$competition->exists ? 'Edit ' . $competition->name : 'New competition'" :back="route('admin.competitions.index')" />

    <form method="POST" action="{{ $competition->exists ? route('admin.competitions.update', $competition) : route('admin.competitions.store') }}" enctype="multipart/form-data" class="card max-w-3xl p-5 sm:p-8">
        @csrf
        @if ($competition->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.field name="name" label="Competition name" :value="$competition->name" placeholder="e.g. BOUESTI Football League" required class="sm:col-span-2" />
            <x-admin.field name="season_id" label="Season" type="select" :options="$seasons" :value="$competition->season_id" empty="Choose a season" required />
            <x-admin.field name="type" label="Type" type="select" :options="\App\Models\Competition::TYPES" :value="$competition->type" required help="Leagues and tournaments get a standings table." />
            <x-admin.field name="short_name" label="Short name" :value="$competition->short_name" placeholder="e.g. BFL" />
            <x-admin.field name="logo" label="Logo" type="file" accept="image/png,image/jpeg,image/webp" :preview="$competition->logo_url" removable help="PNG, JPG or WebP, up to 2 MB." />
            <x-admin.field name="start_date" label="Start date" type="date" :value="$competition->start_date?->format('Y-m-d')" />
            <x-admin.field name="end_date" label="End date" type="date" :value="$competition->end_date?->format('Y-m-d')" />
            <x-admin.field name="description" label="Description" type="textarea" :value="$competition->description" class="sm:col-span-2" />
            <x-admin.field name="is_active" label="Active" type="checkbox" :value="$competition->is_active" help="Inactive competitions are hidden from public filters." class="sm:col-span-2" />
        </div>

        <x-admin.form-actions :cancel="route('admin.competitions.index')" :label="$competition->exists ? 'Save changes' : 'Create competition'" />
    </form>
</x-layouts.admin>
