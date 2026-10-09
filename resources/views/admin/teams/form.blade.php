<x-layouts.admin :title="$team->exists ? 'Edit team' : 'New team'">
    <x-admin.page-header :title="$team->exists ? 'Edit ' . $team->name : 'New team'" :back="$team->exists ? route('admin.teams.show', $team) : route('admin.teams.index')" />

    <form method="POST" action="{{ $team->exists ? route('admin.teams.update', $team) : route('admin.teams.store') }}" enctype="multipart/form-data" class="card max-w-3xl p-5 sm:p-8">
        @csrf
        @if ($team->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.field name="name" label="Team name" :value="$team->name" placeholder="e.g. Amapro FC" required />
            <x-admin.field name="short_name" label="Short name" :value="$team->short_name" placeholder="e.g. AMA" maxlength="12" help="Shown on badges when there is no logo." />
            <x-admin.field name="logo" label="Logo" type="file" accept="image/png,image/jpeg,image/webp" :preview="$team->logo_url" removable help="PNG, JPG or WebP, up to 2 MB. Square images work best." class="sm:col-span-2" />
            <x-admin.field name="primary_color" label="Primary colour" type="color" :value="$team->primary_color ?? '#009a56'" class="[&_input]:h-12 [&_input]:p-1.5" />
            <x-admin.field name="secondary_color" label="Secondary colour" type="color" :value="$team->secondary_color ?? '#d9ad45'" class="[&_input]:h-12 [&_input]:p-1.5" />
            <x-admin.field name="coach_name" label="Coach" :value="$team->coach_name" />
            <x-admin.field name="captain_name" label="Captain" :value="$team->captain_name" help="Or tick “Captain” on a player’s profile." />
            <x-admin.field name="founded_year" label="Founded (year)" type="number" :value="$team->founded_year" min="1950" max="{{ date('Y') }}" inputmode="numeric" />
            <x-admin.field name="description" label="Description" type="textarea" :value="$team->description" class="sm:col-span-2" />
            <x-admin.field name="is_active" label="Active" type="checkbox" :value="$team->is_active" help="Inactive teams are hidden from public lists and new fixtures." class="sm:col-span-2" />
        </div>

        <x-admin.form-actions :cancel="$team->exists ? route('admin.teams.show', $team) : route('admin.teams.index')" :label="$team->exists ? 'Save changes' : 'Create team'" />
    </form>
</x-layouts.admin>
