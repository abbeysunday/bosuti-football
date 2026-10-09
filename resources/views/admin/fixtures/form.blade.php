<x-layouts.admin :title="$fixture->exists ? 'Edit fixture' : 'New fixture'">
    <x-admin.page-header :title="$fixture->exists ? 'Edit fixture' : 'New fixture'" :description="$fixture->exists ? $fixture->title : 'Schedule a match between two BOUESTI teams.'" :back="$fixture->exists ? route('admin.fixtures.show', $fixture) : route('admin.fixtures.index')" />

    @if ($competitions->isEmpty())
        <x-alert type="warning" class="mb-6" title="Create a competition first">
            Fixtures belong to a competition. <a href="{{ route('admin.competitions.create') }}" class="link">Create one</a>.
        </x-alert>
    @endif

    <form method="POST" action="{{ $fixture->exists ? route('admin.fixtures.update', $fixture) : route('admin.fixtures.store') }}" class="max-w-4xl space-y-6"
          x-data="{ status: @js(old('status', $fixture->status)), home: @js((string) old('home_team_id', $fixture->home_team_id)), away: @js((string) old('away_team_id', $fixture->away_team_id)) }">
        @csrf
        @if ($fixture->exists) @method('PUT') @endif

        <x-admin.section title="Match">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field name="competition_id" label="Competition" type="select" :options="$competitions" :value="$fixture->competition_id" empty="Choose a competition" required class="sm:col-span-2" help="The season is taken from the competition." />
                <x-admin.field name="home_team_id" label="Home team" type="select" :options="$teams" :value="$fixture->home_team_id" empty="Choose home team" required x-model="home" />
                <x-admin.field name="away_team_id" label="Away team" type="select" :options="$teams" :value="$fixture->away_team_id" empty="Choose away team" required x-model="away" />
                <p x-cloak x-show="home && home === away" class="form-error sm:col-span-2" role="alert">A team cannot play against itself. Choose two different teams.</p>
                <x-admin.field name="match_date" label="Date" type="date" :value="$fixture->match_date?->format('Y-m-d')" required />
                <x-admin.field name="kickoff_time" label="Kick-off time" type="time" :value="$fixture->kickoff_time ? substr($fixture->kickoff_time, 0, 5) : null" />
                <x-admin.field name="venue" label="Venue" :value="$fixture->venue" placeholder="e.g. BOUESTI Sports Complex" />
                <x-admin.field name="matchday" label="Matchday" type="number" :value="$fixture->matchday" min="1" inputmode="numeric" />
                <x-admin.field name="featured" label="Featured match" type="checkbox" :value="$fixture->featured" help="Highlighted on the homepage when it is the next match." class="sm:col-span-2" />
            </div>
        </x-admin.section>

        <x-admin.section title="Status & result" description="Scores are only saved for live or completed matches. Completed results update the league table automatically.">
            <div class="grid gap-5 sm:grid-cols-3">
                <x-admin.field name="status" label="Status" type="select" :options="\App\Models\Fixture::STATUSES" :value="$fixture->status" required x-model="status" />
                <div x-show="['live', 'completed'].includes(status)" x-cloak class="contents">
                    <x-admin.field name="home_score" label="Home score" type="number" :value="$fixture->home_score" min="0" max="99" inputmode="numeric" />
                    <x-admin.field name="away_score" label="Away score" type="number" :value="$fixture->away_score" min="0" max="99" inputmode="numeric" />
                </div>
                <x-admin.field name="referee" label="Referee" :value="$fixture->referee" />
                <x-admin.field name="attendance" label="Attendance" type="number" :value="$fixture->attendance" min="0" inputmode="numeric" />
            </div>
        </x-admin.section>

        <x-admin.form-actions :cancel="$fixture->exists ? route('admin.fixtures.show', $fixture) : route('admin.fixtures.index')" :label="$fixture->exists ? 'Save fixture' : 'Create fixture'" />
    </form>
</x-layouts.admin>
