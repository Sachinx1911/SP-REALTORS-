<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ $title ?? setting('seo_title') }}</title>
	<meta name="description" content="{{ $description ?? setting('seo_description') }}">
	<link rel="canonical" href="{{ url()->current() }}">

	{{-- Open Graph / Twitter --}}
	<meta property="og:type" content="{{ $ogType ?? 'website' }}">
	<meta property="og:site_name" content="{{ setting('site_name') }}">
	<meta property="og:title" content="{{ $title ?? setting('seo_title') }}">
	<meta property="og:description" content="{{ $description ?? setting('seo_description') }}">
	<meta property="og:url" content="{{ url()->current() }}">
	@isset($ogImage)
		<meta property="og:image" content="{{ $ogImage }}">
	@endisset
	<meta name="twitter:card" content="summary_large_image">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">

	@vite(['resources/css/app.css', 'resources/js/app.js'])

	{{-- LocalBusiness schema on every page --}}
	@php
		$businessSchema = [
			'@context' => 'https://schema.org',
			'@type' => 'RealEstateAgent',
			'name' => setting('site_name'),
			'description' => setting('seo_description'),
			'url' => url('/'),
			'telephone' => setting('phone'),
			'email' => setting('email'),
			'areaServed' => 'Navi Mumbai, Maharashtra, India',
			'address' => [
				'@type' => 'PostalAddress',
				'addressLocality' => 'Navi Mumbai',
				'addressRegion' => 'Maharashtra',
				'addressCountry' => 'IN',
				'streetAddress' => setting('address'),
			],
		];
	@endphp
	<script type="application/ld+json">
		{!! json_encode($businessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
	</script>

	@stack('head')
</head>
<body class="antialiased {{ isset($stickyCta) ? 'pb-[60px] lg:pb-0' : '' }}">

	<x-site.header />

	<main id="main">
		{{ $slot }}
	</main>

	<x-site.footer />

	@isset($stickyCta)
		{{ $stickyCta }}
	@endisset

	@stack('scripts')
</body>
</html>
