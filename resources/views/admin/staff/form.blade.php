<x-layouts.admin :title="$member->exists ? 'Edit staff member' : 'Add staff member'">
    <x-admin.page-header :title="$member->exists ? 'Edit ' . $member->name : 'Add staff member'" :back="route('admin.staff.index')" />

    <form method="POST" action="{{ $member->exists ? route('admin.staff.update', $member) : route('admin.staff.store') }}" enctype="multipart/form-data" class="card max-w-3xl p-5 sm:p-8">
        @csrf
        @if ($member->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.field name="name" label="Full name" :value="$member->name" required />
            <x-admin.field name="role" label="Role" :value="$member->role" placeholder="e.g. Head Coach" required />
            <x-admin.field name="type" label="Type" type="select" :options="\App\Models\Staff::TYPES" :value="$member->type" required help="Management appears on the Management page; coaching on Coaching Staff." />
            <x-admin.field name="team_id" label="Team" type="select" :options="$teams" :value="$member->team_id" empty="All teams / football programme" />
            <x-admin.field name="photo" label="Photo" type="file" accept="image/png,image/jpeg,image/webp" :preview="$member->photo_url" removable help="Official portrait, up to 3 MB." class="sm:col-span-2" />
            <x-admin.field name="bio" label="Biography" type="textarea" :value="$member->bio" class="sm:col-span-2" />
            <x-admin.field name="sort_order" label="Sort order" type="number" :value="$member->sort_order" inputmode="numeric" help="Lower numbers appear first." />
            <x-admin.field name="is_active" label="Show on website" type="checkbox" :value="$member->is_active" />
        </div>

        <x-admin.form-actions :cancel="route('admin.staff.index')" :label="$member->exists ? 'Save changes' : 'Add staff member'" />
    </form>
</x-layouts.admin>
