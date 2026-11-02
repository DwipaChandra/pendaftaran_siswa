@props(['type' => 'error'])

@php
$typeClasses = match($type) {
    'error' => 'bg-rose-50 border-rose-200 text-rose-700',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'success' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
    default => 'bg-blue-50 border-blue-200 text-blue-700',
};
@endphp

<div {{ $attributes->merge(['class' => "p-3 sm:p-4 rounded-xl sm:rounded-2xl border text-xs sm:text-sm font-medium leading-relaxed {$typeClasses}"]) }} role="alert">
    {{ $slot }}
</div>

