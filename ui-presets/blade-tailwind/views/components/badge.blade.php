@props([
    'type' => 'default'
])

@php
switch($type) {
    case('info'):
        $color = 'bg-blue-500';
        break;

    case('danger'):
        $color = 'bg-red-500';
        break;

    case('warning'):
        $color = 'bg-orange-500';
        break;

    default:
        $color = 'bg-gray-400';
        break;
}
@endphp

<span {{ $attributes->merge(['class' => "$color rounded-full px-2 py-1 text-white text-xs font-semibold"]) }}>
    {{ $slot }}
</span>
