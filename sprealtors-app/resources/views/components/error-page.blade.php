@props(['code', 'icon', 'heading', 'text'])

@php
    // Error views render outside the normal controller flow, so the search
    // form's location list is fetched here directly. Guarded: if the database
    // itself is the problem (e.g. on the 500 page), the error page must still
    // render — just without the location dropdown.
    try {
        $errorPageLocations = \App\Models\Location::published()->ordered()->get();
    } catch (\Throwable $e) {
        $errorPageLocations = collect();
    }
@endphp

<x-layouts.site :title="$code.' — '.$heading.' | SP REALTORS'" description="{{ $text }}">

	<section class="site-container py-16 lg:py-24">
		<div class="max-w-[560px] mx-auto text-center">
			<span class="inline-flex w-16 h-16 rounded-full bg-lightblue text-blue items-center justify-center mb-6">
				<x-icon :name="$icon" class="w-7 h-7" />
			</span>

			<p class="font-display text-[15px] tracking-[0.15em] text-gold uppercase mb-2">Error {{ $code }}</p>
			<h1 class="font-display text-[32px] lg:text-[40px] text-navy mb-3">{{ $heading }}</h1>
			<p class="text-[15px] text-muted mb-8">{{ $text }}</p>

			<x-search-form :locations="$errorPageLocations" class="mb-8 text-left" />

			<div class="flex flex-wrap items-center justify-center gap-3 mb-10">
				<a href="{{ route('home') }}" class="btn btn-primary">
					<x-icon name="home" class="w-[18px] h-[18px]" />
					Go to Homepage
				</a>
				<a href="{{ route('properties.index') }}" class="btn btn-outline">
					Browse Properties
				</a>
			</div>

			<p class="text-[13px] text-muted">
				Still stuck? Call us at
				<a href="{{ tel_url() }}" class="text-blue font-semibold hover:underline">{{ setting('phone') }}</a>
				or
				<a href="{{ whatsapp_url('Hello SP REALTORS, I ran into a problem (Error '.$code.') on your website.') }}" class="text-blue font-semibold hover:underline" target="_blank" rel="noopener">message us on WhatsApp</a>.
			</p>
		</div>
	</section>

</x-layouts.site>
