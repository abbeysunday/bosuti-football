@props(['name', 'label', 'options' => [], 'all' => 'All'])

<label class="min-w-0 sm:w-48">
    <span class="sr-only">{{ $label }}</span>
    <select name="{{ $name }}" class="form-input min-h-[44px]" aria-label="{{ $label }}">
        <option value="">{{ $all }}</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) request($name) === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
</label>
