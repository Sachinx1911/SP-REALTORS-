@props([
    'whatsappUrl' => null,
    'telUrl' => null,
    'secondaryLabel' => 'Call',
    'secondaryHref' => null,
])

<div class="lg:hidden fixed inset-x-0 bottom-0 z-40 flex bg-white border-t border-line shadow-[0_-4px_12px_rgba(9,43,80,0.06)]">
	<a href="{{ $whatsappUrl ?? whatsapp_url() }}" target="_blank" rel="noopener"
	   class="btn btn-whatsapp flex-1 rounded-none h-[60px]">
		<x-icon name="whatsapp" class="w-5 h-5" />
		WhatsApp
	</a>
	<a href="{{ $secondaryHref ?? $telUrl ?? tel_url() }}"
	   class="btn btn-primary flex-1 rounded-none h-[60px]">
		<x-icon name="phone" class="w-5 h-5" />
		{{ $secondaryLabel }}
	</a>
</div>
