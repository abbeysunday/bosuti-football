@props(['cancel', 'label' => 'Save', 'loading' => 'Saving…'])

<div class="sticky bottom-0 z-10 -mx-4 mt-8 flex flex-col-reverse gap-3 border-t border-subtle bg-pitch-950/95 px-4 py-4 backdrop-blur sm:static sm:mx-0 sm:flex-row sm:justify-end sm:border-0 sm:bg-transparent sm:p-0">
    {{ $slot }}
    <a href="{{ $cancel }}" class="btn btn-outline">Cancel</a>
    <button type="submit" class="btn btn-primary" data-loading-text="{{ $loading }}">{{ $label }}</button>
</div>
