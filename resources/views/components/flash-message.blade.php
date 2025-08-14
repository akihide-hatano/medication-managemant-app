@props([
    'type' => session('type', 'ok'), // ok, ng, warn のどれか
    'message' => session('message') ?? session('ok') ?? session('ng') ?? session('warn')
])

@php
    $colors = [
        'ok'   => 'bg-green-50 text-green-700 border-green-200',
        'ng'   => 'bg-red-50 text-red-700 border-red-200',
        'warn' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    ];
@endphp

@if ($message)
    <div class="mb-4 rounded-lg border p-3 {{ $colors[$type] ?? $colors['ok'] }}">
        {{ $message }}
    </div>
@endif
