<x-layouts.admin :title="$player->exists ? 'Edit player' : 'Add player'">
    <x-admin.page-header :title="$player->exists ? 'Edit ' . $player->full_name : 'Add player'" :back="route('admin.players.index', ['team' => $player->team_id])" />

    <form method="POST" action="{{ $player->exists ? route('admin.players.update', $player) : route('admin.players.store') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        @if ($player->exists) @method('PUT') @endif

        <x-admin.section title="Player">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field name="team_id" label="Team" type="select" :options="$teams" :value="$player->team_id" empty="Choose a team" required />
                <x-admin.field name="position" label="Position" type="select" :options="\App\Models\Player::POSITIONS" :value="$player->position" empty="Choose a position" required />
                <x-admin.field name="first_name" label="First name" :value="$player->first_name" autocomplete="off" required />
                <x-admin.field name="last_name" label="Last name" :value="$player->last_name" autocomplete="off" required />
                <x-admin.field name="jersey_number" label="Jersey number" type="number" :value="$player->jersey_number" min="1" max="99" inputmode="numeric" help="Unique within the team." />
                <x-admin.field name="dominant_foot" label="Dominant foot" type="select" :options="\App\Models\Player::FEET" :value="$player->dominant_foot" empty="Not specified" />
                <x-admin.field name="height" label="Height" :value="$player->height" placeholder="e.g. 1.82 m" />
                <x-admin.field name="photo" label="Photo" type="file" accept="image/png,image/jpeg,image/webp" :preview="$player->photo_url" removable help="Portrait photo, up to 3 MB." />
                <x-admin.field name="bio" label="Biography" type="textarea" :value="$player->bio" help="Shown on the public player profile." class="sm:col-span-2" />
            </div>
        </x-admin.section>

        <x-admin.section title="Student details" description="Department and level appear publicly. Matric number and state of origin are for admins only.">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field name="department" label="Department" :value="$player->department" />
                <x-admin.field name="level" label="Level" type="select" :options="array_combine(\App\Models\Player::LEVELS, \App\Models\Player::LEVELS)" :value="$player->level" empty="Not specified" />
                <x-admin.field name="matric_number" label="Matric number (private)" :value="$player->matric_number" autocomplete="off" />
                <x-admin.field name="state_of_origin" label="State of origin (private)" :value="$player->state_of_origin" />
            </div>
        </x-admin.section>

        <x-admin.section title="Visibility">
            <div class="grid gap-3 sm:grid-cols-3">
                <x-admin.field name="is_captain" label="Team captain" type="checkbox" :value="$player->is_captain" />
                <x-admin.field name="is_featured" label="Featured player" type="checkbox" :value="$player->is_featured" help="Shown on the homepage." />
                <x-admin.field name="is_active" label="Active" type="checkbox" :value="$player->is_active" help="Inactive players leave the public squad." />
            </div>
        </x-admin.section>

        <x-admin.form-actions :cancel="route('admin.players.index', ['team' => $player->team_id])" :label="$player->exists ? 'Save changes' : 'Add player'">
            @unless ($player->exists)
                <button type="submit" name="add_another" value="1" class="btn btn-secondary">Save &amp; add another</button>
            @endunless
        </x-admin.form-actions>
    </form>
</x-layouts.admin>
