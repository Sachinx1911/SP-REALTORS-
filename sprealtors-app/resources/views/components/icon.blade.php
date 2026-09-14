@props(['name', 'class' => 'w-5 h-5'])

@php
    // Lucide-style outline icons, stroke 1.8. Single source so no icon library is loaded.
    $stroke = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';

    $paths = [
        'home' => '<path d="m3 10 9-7 9 7v9a2 2 0 0 1-2 2h-4v-6H9v6H5a2 2 0 0 1-2-2z"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'close' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'whatsapp' => '<path d="M21 11.5a8.4 8.4 0 0 1-12.6 7.3L3 20.5l1.8-5.2A8.5 8.5 0 1 1 21 11.5z"/><path d="M8.6 9.2c.2-.5.4-.5.6-.5h.5c.2 0 .4 0 .6.5l.6 1.4c.1.2 0 .4-.1.5l-.4.5c-.1.1-.2.3-.1.5.4.7 1.2 1.6 2.1 2 .2.1.4.1.5-.1l.5-.6c.1-.2.3-.2.5-.1l1.4.7c.2.1.3.2.3.4 0 .5-.3 1.3-.8 1.5-.4.2-1 .3-1.5.1a8 8 0 0 1-4.5-3.7c-.3-.5-.6-1.2-.6-1.8 0-.5.2-1 .4-1.2z"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/>',
        'map-pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'check-circle' => '<circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/>',
        'check' => '<path d="m5 12 5 5L20 7"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-1.8-.3 1.6 1.6 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1A1.6 1.6 0 0 0 9 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0 .3-1.8 1.6 1.6 0 0 0-1.5-1H3a2 2 0 0 1 0-4h.1A1.6 1.6 0 0 0 4.6 9a1.6 1.6 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.6 1.6 0 0 0 1.8.3H9a1.6 1.6 0 0 0 1-1.5V3a2 2 0 0 1 4 0v.1a1.6 1.6 0 0 0 1 1.5 1.6 1.6 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V9a1.6 1.6 0 0 0 1.5 1H21a2 2 0 0 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1z"/>',
        'file-text' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>',
        'headset' => '<path d="M3 14v-3a9 9 0 0 1 18 0v3"/><path d="M21 16a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2zM3 16a2 2 0 0 0 2 2h1v-6H5a2 2 0 0 0-2 2z"/>',
        'key' => '<circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 8.3-8.3M17 6l2 2M14 9l2 2"/>',
        'building' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
        'store' => '<path d="M3 9 4.5 4h15L21 9M4 9v11a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9M3 9h18a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0z"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'area' => '<path d="M3 8V5a2 2 0 0 1 2-2h3M16 3h3a2 2 0 0 1 2 2v3M21 16v3a2 2 0 0 1-2 2h-3M8 21H5a2 2 0 0 1-2-2v-3"/>',
        'bed' => '<path d="M2 17v-5h20v5M2 17v3M22 17v3M2 12V7M6 12V9a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/>',
        'bath' => '<path d="M4 12V5a2 2 0 0 1 4 0M2 12h20v3a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4zM6 19l-1 2M18 19l1 2"/>',
        'car' => '<path d="M5 17H3v-5l2-5h14l2 5v5h-2M5 17a2 2 0 1 0 4 0M15 17a2 2 0 1 0 4 0M9 17h6M4 12h16"/>',
        'sofa' => '<path d="M4 11V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4M2 13a2 2 0 0 1 4 0v3h12v-3a2 2 0 0 1 4 0v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/>',
        'award' => '<circle cx="12" cy="8" r="6"/><path d="m8.2 13.9-1.2 7.1 5-3 5 3-1.2-7.1"/>',
        'shield' => '<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/>',
        'trending-up' => '<path d="m3 17 6-6 4 4 8-8"/><path d="M17 7h4v4"/>',
        'handshake' => '<path d="m11 17 2 2a1 1 0 0 0 1.4 0l2.6-2.6M3 12l3-3 4 4 2-2 4 4 3-3M2 9l4-4 3 3M22 9l-4-4-3 3"/>',
        'quote' => '<path d="M6 17h3l2-4V7H5v6h3zM15 17h3l2-4V7h-6v6h3z"/>',
        'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
        'chevron-left' => '<path d="m15 18-6-6 6-6"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'share' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
        'download' => '<path d="M12 3v12M7 11l5 5 5-5M4 21h16"/>',
        'send' => '<path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"/>',
        'lock' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'facebook' => '<path d="M14 9V7a2 2 0 0 1 2-2h2V2h-3a5 5 0 0 0-5 5v2H7v3h3v10h4V12h3l1-3z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>',
        'linkedin' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-11h4v1.5A6 6 0 0 1 16 8z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
        'youtube' => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3z"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
        'filter' => '<path d="M3 4h18l-7 8v6l-4 2v-8z"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'pencil' => '<path d="M17 3a2.8 2.8 0 0 1 4 4L7.5 20.5 2 22l1.5-5.5z"/>',
        'trash' => '<path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        'eye' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
        'inbox' => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5h13l3.5 7v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6z"/>',
        'layout-dashboard' => '<rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'dumbbell' => '<path d="M6.5 6.5v11M17.5 6.5v11M3 9v6M21 9v6M6.5 12h11"/>',
        'waves' => '<path d="M2 6c2 0 2 1.5 4 1.5S8 6 10 6s2 1.5 4 1.5S16 6 18 6s2 1.5 4 1.5M2 12c2 0 2 1.5 4 1.5s2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5M2 18c2 0 2 1.5 4 1.5s2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5"/>',
        'baby' => '<circle cx="12" cy="7" r="4"/><path d="M9 13h6l2 8H7z"/>',
        'zap' => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
        'tree' => '<path d="M12 2 6 11h3l-4 6h5v5h4v-5h5l-4-6h3z"/>',
        'star' => '<path d="m12 2 3 6.5 7 .8-5 4.8 1.3 7L12 17.8 5.7 21l1.3-7-5-4.8 7-.8z"/>',
    ];

    $path = $paths[$name] ?? '';
@endphp

@if($path)
    {!! $stroke.' class="'.e($class).'">'.$path.'</svg>' !!}
@endif
