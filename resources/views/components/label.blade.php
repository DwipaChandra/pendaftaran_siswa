@props(['value' => null])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>

