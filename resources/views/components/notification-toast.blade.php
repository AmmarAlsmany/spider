@props([
    'type' => 'info',
    'title' => '',
    'message' => '',
    'id' => '',
    'autohide' => true,
    'delay' => 5000,
])

@php
    $bgClass = match ($type) {
        'success' => 'bg-success text-white',
        'warning' => 'bg-warning text-dark',
        'error' => 'bg-danger text-white',
        'info' => 'bg-info text-white',
        default => 'bg-primary text-white',
    };

    $iconClass = match ($type) {
        'success' => 'bx bx-check-circle',
        'warning' => 'bx bx-error',
        'error' => 'bx bx-x-circle',
        'info' => 'bx bx-info-circle',
        default => 'bx bx-bell',
    };
@endphp

<div {{ $attributes->merge(['class' => 'toast', 'role' => 'alert']) }} id="{{ $id }}"
    data-bs-autohide="{{ $autohide ? 'true' : 'false' }}" data-bs-delay="{{ $delay }}">
    <div class="toast-header {{ $bgClass }}">
        <i class="{{ $iconClass }} me-2"></i>
        <strong class="me-auto">{{ $title }}</strong>
        <small>{{ __('Just now') }}</small>
        <button type="button" class="btn-close {{ $type === 'warning' ? '' : 'btn-close-white' }}" data-bs-dismiss="toast"
            aria-label="Close"></button>
    </div>
    <div class="toast-body">
        {{ $message }}
        {{ $slot }}
    </div>
</div>
