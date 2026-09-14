@php
    $gallery = $property->gallery();
    $schema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'RealEstateListing',
        'name' => $property->title,
        'url' => route('properties.show', $property),
        'description' => $property->description,
        'datePosted' => $property->created_at?->toIso8601String(),
        'image' => $property->mainImageUrl(),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $property->address,
            'addressLocality' => $property->location?->name,
            'addressRegion' => 'Maharashtra',
            'addressCountry' => 'IN',
        ],
        'offers' => $property->price ? [
            '@type' => 'Offer',
            'price' => (float) $property->price,
            'priceCurrency' => 'INR',
            'availability' => in_array($property->status, ['sold', 'rented'], true)
                ? 'https://schema.org/SoldOut'
                : 'https://schema.org/InStock',
        ] : null,
    ]);
@endphp

<x-layouts.site
	:title="$property->seo_title ?: $property->title.' | SP REALTORS'"
	:description="$property->seo_description ?: Str::limit(strip_tags((string) $property->description), 155)"
	:og-image="$property->mainImageUrl()"
	og-type="article">

	@push('head')
		<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
	@endpush

	<div class="site-container">
		<x-breadcrumbs :items="[
			['label' => 'Properties', 'url' => route('properties.index')],
			['label' => $property->title],
		]" />
	</div>

	<div class="site-container pb-12">
		<div class="grid gap-8 lg:grid-cols-[1fr_380px]">

			{{-- LEFT --}}
			<div>
				{{-- Gallery --}}
				@if($gallery)
					<div class="mb-6" data-gallery>
						<div class="relative rounded-[10px] overflow-hidden bg-lightgray">
							<img src="{{ $gallery[0]['url'] }}" alt="{{ $gallery[0]['alt'] }}"
							     class="w-full aspect-[760/500] object-cover" data-gallery-main fetchpriority="high">

							@if(count($gallery) > 1)
								<button type="button" data-gallery-prev
								        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 text-navy flex items-center justify-center shadow-[var(--shadow-soft)] hover:bg-white">
									<span class="sr-only">Previous image</span>
									<x-icon name="chevron-left" class="w-5 h-5" />
								</button>
								<button type="button" data-gallery-next
								        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 text-navy flex items-center justify-center shadow-[var(--shadow-soft)] hover:bg-white">
									<span class="sr-only">Next image</span>
									<x-icon name="chevron-right" class="w-5 h-5" />
								</button>
								<span data-gallery-counter
								      class="absolute bottom-3 right-3 bg-black/60 text-white text-[12px] px-3 py-1 rounded-full">
									1 / {{ count($gallery) }}
								</span>
							@endif
						</div>

						@if(count($gallery) > 1)
							<div class="flex gap-2 mt-3 overflow-x-auto no-scrollbar">
								@foreach($gallery as $i => $image)
									<button type="button" data-gallery-thumb
									        data-full="{{ $image['url'] }}" data-alt="{{ $image['alt'] }}"
									        class="shrink-0 w-[90px] aspect-[4/3] rounded-[4px] overflow-hidden border-2 {{ $i === 0 ? 'border-blue' : 'border-transparent' }}">
										<img src="{{ $image['url'] }}" alt="" loading="lazy" class="w-full h-full object-cover">
									</button>
								@endforeach
							</div>
						@endif
					</div>
				@endif

				{{-- Header --}}
				<header>
					<div class="flex items-start justify-between gap-3">
						<div class="flex flex-wrap items-center gap-3">
							<h1 class="text-[26px] lg:text-[32px] m-0">{{ $property->title }}</h1>
							<span class="badge {{ $property->badgeClass() }}">{{ $property->badgeLabel() }}</span>
							<span class="badge bg-transparent border-[1.5px] border-navy text-navy">{{ $property->typeLabel() }}</span>
						</div>

						<button type="button" data-share aria-label="Share this property"
						        class="shrink-0 w-10 h-10 rounded-full border border-line text-muted flex items-center justify-center hover:text-blue hover:border-blue transition-colors">
							<x-icon name="share" class="w-[18px] h-[18px]" />
						</button>
					</div>

					<p class="flex items-center gap-1.5 text-[14px] text-muted mt-2">
						<x-icon name="map-pin" class="w-4 h-4" />
						{{ $property->locationLabel() }}
					</p>

					<p class="text-[26px] font-bold text-blue mt-3">
						{{ $property->formattedPrice() }}
						@if($property->price_negotiable)
							<span class="text-[14px] font-normal text-muted">(Negotiable)</span>
						@endif
					</p>

					{{-- Specs --}}
					<ul class="flex flex-wrap gap-5 border-y border-line py-4 mt-4">
						@foreach(array_filter([
							$property->area ? ['area', number_format($property->area).' '.$property->area_unit, 'Carpet Area'] : null,
							$property->bedrooms ? ['bed', $property->bedrooms.' '.Str::plural('Bedroom', $property->bedrooms), null] : null,
							$property->bathrooms ? ['bath', $property->bathrooms.' '.Str::plural('Bathroom', $property->bathrooms), null] : null,
							$property->furnishingLabel() ? ['sofa', $property->furnishingLabel(), null] : null,
							$property->car_parking ? ['car', $property->car_parking.' Car Parking', null] : null,
						]) as [$icon, $label, $sub])
							<li class="flex items-center gap-2">
								<span class="w-9 h-9 rounded-full bg-lightgray text-blue flex items-center justify-center shrink-0">
									<x-icon :name="$icon" class="w-[18px] h-[18px]" />
								</span>
								<span class="text-[13px]">
									<strong class="block text-ink font-semibold">{{ $label }}</strong>
									@if($sub)<small class="block text-[11px] text-muted">{{ $sub }}</small>@endif
								</span>
							</li>
						@endforeach
					</ul>
				</header>

				{{-- Overview --}}
				<section class="mt-8">
					<h2 class="text-[20px] mb-3">Property Overview</h2>
					<div class="card overflow-hidden">
						<dl class="grid sm:grid-cols-2 text-[13px]">
							@foreach(array_filter([
								'Property Type' => $property->typeLabel(),
								'Area' => $property->area ? number_format($property->area).' '.$property->area_unit : null,
								'Configuration' => $property->configurationLabel(),
								'Status' => $property->statusLabel(),
								'Furnishing' => $property->furnishingLabel(),
								'RERA Number' => $property->rera_number,
								'Possession' => $property->possession,
							]) as $label => $value)
								<div class="flex gap-3 px-4 py-3 border-b border-line odd:sm:border-r">
									<dt class="w-[45%] text-muted shrink-0">{{ $label }}</dt>
									<dd class="font-semibold text-ink m-0">{{ $value }}</dd>
								</div>
							@endforeach
						</dl>
					</div>
				</section>

				{{-- Description --}}
				@if($property->description)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Description</h2>
						<p class="text-[14px] text-body whitespace-pre-line">{{ $property->description }}</p>
					</section>
				@endif

				{{-- Highlights --}}
				@if($property->highlights)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Property Highlights</h2>
						<ul class="grid sm:grid-cols-2 gap-2">
							@foreach($property->highlights as $highlight)
								<li class="flex items-center gap-2 text-[14px] text-body">
									<span class="text-green shrink-0"><x-icon name="check" class="w-[18px] h-[18px]" /></span>
									{{ $highlight }}
								</li>
							@endforeach
						</ul>
					</section>
				@endif

				{{-- Amenities --}}
				@if($property->amenities)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Amenities</h2>
						<ul class="grid grid-cols-2 sm:grid-cols-3 gap-4">
							@foreach($property->amenities as $amenity)
								<li class="flex items-center gap-2 text-[13px] text-body">
									<span class="w-10 h-10 rounded-full bg-lightgray text-blue flex items-center justify-center shrink-0">
										<x-icon :name="\App\Support\Amenities::icon($amenity)" class="w-5 h-5" />
									</span>
									{{ $amenity }}
								</li>
							@endforeach
						</ul>
					</section>
				@endif

				{{-- Location --}}
				@if($property->map_url || $property->address)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Location</h2>
						<div class="grid gap-4 md:grid-cols-[1fr_280px]">
							@if($property->map_url)
								<div class="rounded-[8px] overflow-hidden aspect-[16/10] bg-lightgray">
									<iframe src="{{ $property->map_url }}" title="Map of {{ $property->title }}"
									        class="w-full h-full border-0" loading="lazy"
									        referrerpolicy="no-referrer-when-downgrade"></iframe>
								</div>
							@endif
							<div class="card p-4 flex flex-col gap-3">
								<p class="flex items-start gap-2 text-[13px] text-body m-0">
									<span class="text-blue shrink-0 mt-0.5"><x-icon name="map-pin" class="w-[18px] h-[18px]" /></span>
									{{ $property->address ?: $property->locationLabel() }}
								</p>
								@if($property->map_url)
									<a href="{{ $property->map_url }}" target="_blank" rel="noopener" class="btn btn-outline w-full">
										View on Google Maps
									</a>
								@endif
							</div>
						</div>
					</section>
				@endif

				{{-- Related --}}
				@if($related->isNotEmpty())
					<section class="mt-10">
						<div class="flex items-end justify-between gap-4 mb-5">
							<h2 class="text-[20px] lg:text-[24px]">Related Properties</h2>
							<a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-blue hover:underline shrink-0">
								View All Properties <x-icon name="arrow-right" class="w-4 h-4" />
							</a>
						</div>
						<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
							@foreach($related->take(3) as $item)
								<x-property-card :property="$item" />
							@endforeach
						</div>
					</section>
				@endif
			</div>

			{{-- RIGHT: enquiry card --}}
			<aside>
				<div class="card p-5 lg:sticky lg:top-24">
					<x-enquiry-form
						source="property"
						:property="$property"
						title="Enquire About This Property"
						button-label="Send Enquiry" />

					<div class="flex flex-col gap-2 mt-4">
						<a href="{{ $property->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full">
							<x-icon name="whatsapp" class="w-[18px] h-[18px]" />
							Send on WhatsApp
						</a>
						<a href="{{ $property->telUrl() }}" class="btn btn-outline w-full">
							<x-icon name="phone" class="w-[18px] h-[18px]" />
							Call Now
						</a>
					</div>
				</div>
			</aside>
		</div>
	</div>

	<x-slot:sticky-cta>
		<x-mobile-sticky-cta :whatsapp-url="$property->whatsappUrl()" :tel-url="$property->telUrl()" />
	</x-slot:sticky-cta>
</x-layouts.site>
