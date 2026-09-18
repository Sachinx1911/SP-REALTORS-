<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	@php
		// Decode first so an entity written in a title (e.g. "&amp;") is not
		// escaped twice by Blade into "&amp;amp;".
		$metaTitle = html_entity_decode((string) ($title ?? setting('seo_title')), ENT_QUOTES, 'UTF-8');
		$metaDescription = html_entity_decode((string) ($description ?? setting('seo_description')), ENT_QUOTES, 'UTF-8');
		$metaImage = $ogImage ?? asset('images/hero-building.jpg');

		// Keep pagination in the canonical so deep pages stay indexable, but
		// drop filter/sort params — those are near-duplicates of the base page.
		$canonical = request()->has('page')
			? url()->current().'?page='.(int) request('page')
			: url()->current();

		// Faceted result pages should not be indexed at all.
		$noindex = request()->hasAny(['type', 'configuration', 'budget', 'sort', 'location', 'purpose', 'q', 'status']);
	@endphp

	<title>{{ $metaTitle }}</title>
	<meta name="description" content="{{ $metaDescription }}">
	<link rel="canonical" href="{{ $canonical }}">
	@if($noindex)
		<meta name="robots" content="noindex, follow">
	@else
		<meta name="robots" content="index, follow, max-image-preview:large">
	@endif

	{{-- Open Graph / Twitter --}}
	<meta property="og:type" content="{{ $ogType ?? 'website' }}">
	<meta property="og:locale" content="en_IN">
	<meta property="og:site_name" content="{{ setting('site_name') }}">
	<meta property="og:title" content="{{ $metaTitle }}">
	<meta property="og:description" content="{{ $metaDescription }}">
	<meta property="og:url" content="{{ $canonical }}">
	<meta property="og:image" content="{{ $metaImage }}">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="{{ $metaTitle }}">
	<meta name="twitter:description" content="{{ $metaDescription }}">
	<meta name="twitter:image" content="{{ $metaImage }}">

	<link rel="icon" href="{{ asset('favicon-32.png') }}" type="image/png" sizes="32x32">
	<link rel="icon" href="{{ asset('favicon-512.png') }}" type="image/png" sizes="512x512">
	<link rel="apple-touch-icon" href="{{ asset('favicon-180.png') }}">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">

	@vite(['resources/css/app.css', 'resources/js/app.js'])

	{{-- LocalBusiness schema on every page --}}
	@php
		$socialProfiles = array_values(array_filter([
			setting('facebook'), setting('instagram'), setting('linkedin'), setting('youtube'),
		]));

		$businessSchema = array_filter([
			'@context' => 'https://schema.org',
			'@type' => 'RealEstateAgent',
			'@id' => url('/').'#organization',
			'name' => setting('site_name'),
			'description' => $metaDescription,
			'url' => url('/'),
			'logo' => asset('images/logo-full.png'),
			'image' => asset('images/hero-building.jpg'),
			'telephone' => setting('phone'),
			'email' => setting('email'),
			'priceRange' => '₹₹',
			'sameAs' => $socialProfiles ?: null,
			'address' => [
				'@type' => 'PostalAddress',
				'addressLocality' => 'Navi Mumbai',
				'addressRegion' => 'Maharashtra',
				'addressCountry' => 'IN',
				'streetAddress' => setting('address'),
			],
			'areaServed' => [
				'@type' => 'City',
				'name' => 'Navi Mumbai',
			],
			'openingHours' => setting('working_hours'),
		]);

		$websiteSchema = [
			'@context' => 'https://schema.org',
			'@type' => 'WebSite',
			'url' => url('/'),
			'name' => setting('site_name'),
			'potentialAction' => [
				'@type' => 'SearchAction',
				'target' => [
					'@type' => 'EntryPoint',
					'urlTemplate' => route('properties.index').'?q={search_term_string}',
				],
				'query-input' => 'required name=search_term_string',
			],
		];
	@endphp
	<script type="application/ld+json">
		{!! json_encode($businessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
	</script>
	<script type="application/ld+json">
		{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
	</script>

	{{-- Google Analytics --}}
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-WG6TQ0LHHM"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'G-WG6TQ0LHHM');
	</script>

	@stack('head')
</head>
<body class="antialiased {{ isset($stickyCta) ? 'pb-[60px] lg:pb-0' : '' }}">

	{{-- Confirmation for any enquiry submitted from anywhere on the site
	     (popup, brochure gate, contact form) — without this a visitor who
	     submits a modal gets no feedback at all. --}}
	@if(session('enquiry_success'))
		<div data-flash-toast
		     class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-4 sm:max-w-[380px] z-[70]
		            bg-white border border-green/40 rounded-[8px] shadow-[0_16px_40px_rgba(9,43,80,0.18)] p-4
		            flex items-start gap-3"
		     role="status">
			<span class="w-8 h-8 rounded-full bg-green/10 text-green flex items-center justify-center shrink-0">
				<x-icon name="check-circle" class="w-5 h-5" />
			</span>
			<p class="text-[13px] text-ink m-0 grow">{{ session('enquiry_success') }}</p>
			<button type="button" data-flash-close aria-label="Dismiss"
			        class="text-muted hover:text-navy shrink-0 cursor-pointer">
				<x-icon name="close" class="w-4 h-4" />
			</button>
		</div>
	@endif

	<x-site.header />

	<main id="main">
		{{ $slot }}
	</main>

	<x-site.footer />

	@isset($stickyCta)
		{{ $stickyCta }}
	@endisset

	@unless($hideLeadPopup ?? false)
		<x-lead-popup />
	@endunless

	@stack('scripts')
</body>
</html>
