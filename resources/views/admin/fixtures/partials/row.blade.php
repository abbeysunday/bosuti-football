<a href="{{ route('admin.fixtures.show', $fixture) }}" class="-mx-2 grid min-h-[56px] grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] items-center gap-2 rounded-lg px-2 py-2.5 transition hover:bg-white/[0.03] sm:gap-3">
    <x-admin.team :team="$fixture->homeTeam" size="h-7 w-7" reverse class="justify-self-end text-sm" />
    <span class="text-center">
        <span class="block whitespace-nowrap font-display text-lg font-extrabold leading-none {{ $fixture->hasScore() ? 'text-gold-light' : 'text-ink-muted' }}">{{ $fixture->scoreline }}</span>
        <span class="mt-1 block whitespace-nowrap text-[0.6875rem] text-ink-faint">{{ $fixture->match_date->format('d M') }}{{ $fixture->kickoff_label ? ' · ' . $fixture->kickoff_label : '' }}</span>
    </span>
    <x-admin.team :team="$fixture->awayTeam" size="h-7 w-7" class="text-sm" />
</a>
