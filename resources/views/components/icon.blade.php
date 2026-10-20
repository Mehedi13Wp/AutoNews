@props(['name' => 'arrow'])
@php
$paths = [
    'grid' => 'M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z M14 14h7v7h-7z',
    'posts' => 'M8 3h12v17H8z M4 7v14h12 M11 7h6 M11 11h6 M11 15h4',
    'plus' => 'M12 5v14 M5 12h14',
    'edit' => 'm15 5 4 4 M4 20l5-1L21 7a2.8 2.8 0 0 0-4-4L5 15z',
    'category' => 'M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z M14 14h7v7h-7z',
    'source' => 'M5 16a3 3 0 1 0 0 .1 M3 10a11 11 0 0 1 11 11 M3 3a18 18 0 0 1 18 18',
    'arrow' => 'M5 12h14 M13 6l6 6-6 6',
    'external' => 'M14 3h7v7 M21 3 11 13 M10 3H4v17h17v-6',
    'check' => 'm5 12 4 4L19 6',
    'clock' => 'M12 8v5l3 2 M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0',
    'spark' => 'm12 2 2.5 7.5L22 12l-7.5 2.5L12 22l-2.5-7.5L2 12l7.5-2.5z',
    'search' => 'M21 21l-5-5 M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0',
    'menu' => 'M4 6h16 M4 12h16 M4 18h16',
    'close' => 'm6 6 12 12 M6 18 18 6',
    'logout' => 'M9 4H4v16h5 M10 12h11 M17 8l4 4-4 4',
    'globe' => 'M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0 M2 12h20 M12 2c-6 5-6 15 0 20 M12 2c6 5 6 15 0 20',
    'chevron' => 'm9 5 7 7-7 7',
];
@endphp
<svg {{ $attributes->class(['ui-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['arrow'] }}"/></svg>
