@props(['label', 'options', 'param', 'current' => null, 'all' => 'All'])

<div class="filter-bar" role="group" aria-label="{{ $label }}">
    @php $query = request()->except([$param, 'page']); @endphp
    <a href="{{ request()->url() . ($query ? '?' . http_build_query($query) : '') }}" @class(['filter-btn', 'active' => blank($current)]) @if (blank($current)) aria-current="true" @endif>{{ $all }}</a>
    @foreach ($options as $value => $text)
        <a href="{{ request()->url() . '?' . http_build_query($query + [$param => $value]) }}" @class(['filter-btn', 'active' => (string) $current === (string) $value]) @if ((string) $current === (string) $value) aria-current="true" @endif>{{ $text }}</a>
    @endforeach
</div>
