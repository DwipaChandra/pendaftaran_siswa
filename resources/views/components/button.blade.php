@props(['variant' => 'primary', 'type' => 'button'])

@php
$variantClasses = match($variant) {
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-600/30',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-sm',
    default => 'bg-blue-600 hover:bg-blue-700 text-white font-bold',
};
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "px-6 py-2.5 sm:px-8 sm:py-3 rounded-xl text-sm sm:text-base transition inline-flex items-center justify-center gap-2 cursor-pointer {$variantClasses}"]) }}
>
    {{ $slot }}
</button>

