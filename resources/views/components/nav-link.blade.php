@props(['active'])

<a {{ $attributes->class(['side-link', 'is-active' => $active ?? false]) }} @if ($active ?? false) aria-current="page" @endif>
    {{ $slot }}
</a>
