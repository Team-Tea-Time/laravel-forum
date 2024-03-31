@php
$colorClasses = match ($type) {
    'primary', '', null => 'bg-blue-100 text-blue-700',
    'success', '', null => 'bg-green-100 text-green-700',
    'danger' => 'bg-orange-100 text-orange-700'
};
@endphp

<div class="alert alert-{{ $type }} alert-dismissable rounded-md my-4 p-4 flex justify-between gap-4 {{ $colorClasses }}">
    <div class="message">
        {!! $message !!}
    </div>
    <button type="button" data-dismiss="alert" aria-hidden="true">
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
        </svg>
    </button>
</div>
