@props(['disabled' => false, 'error' => false])

<select
    @disabled($disabled)
    {{ $attributes->class([
        'w-full text-sm sm:text-base rounded-xl border px-3.5 py-2.5 sm:py-3 bg-white focus:outline-none transition appearance-none',
        'border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-100' => $error,
        'border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-100' => ! $error,
    ]) }}
>
    {{ $slot }}
</select>

