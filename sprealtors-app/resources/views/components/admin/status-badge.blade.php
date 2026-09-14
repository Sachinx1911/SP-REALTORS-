@props(['status'])

@php
    $styles = [
        'new' => 'bg-green/10 text-green-dark',
        'contacted' => 'bg-blue/10 text-blue',
        'qualified' => 'bg-gold/15 text-gold-dark',
        'closed' => 'bg-lightgray text-muted',
    ];
    $label = \App\Models\Enquiry::statusOptions()[$status] ?? $status;
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] text-[11px] font-bold {{ $styles[$status] ?? 'bg-lightgray text-muted' }}">
	{{ $label }}
</span>
