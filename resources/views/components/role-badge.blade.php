@props(['role'])

@php
$colors = [
    'client' => 'bg-blue-100 text-blue-800',
    'ressourcerie' => 'bg-green-100 text-green-800',
    'admin' => 'bg-purple-100 text-purple-800',
];

$label = match($role) {
    'admin' => 'Admin',
    'ressourcerie' => 'Ressourcerie',
    default => 'Client',
};

$baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
$colorClasses = $colors[$role] ?? $colors['client'];
@endphp

<span class="{{ $baseClasses }} {{ $colorClasses }}">
    {{ $label }}
</span> 