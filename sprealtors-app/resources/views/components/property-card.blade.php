@props(['property', 'heading' => 'h3'])

<article class="card card-hover overflow-hidden flex flex-col">
	<a href="{{ route('properties.show', $property) }}" class="relative block aspect-[413/190] bg-lightblue" tabindex="-1" aria-hidden="true">
		@if($property->mainImageUrl())
			<img src="{{ $property->mainImageUrl() }}" alt="" loading="lazy"
			     class="w-full h-full object-cover">
		@else
			<span class="w-full h-full flex items-center justify-center text-blue/40">
				<x-icon name="building" class="w-12 h-12" />
			</span>
		@endif

		<span class="badge {{ $property->badgeClass() }} absolute top-3 left-3">{{ $property->badgeLabel() }}</span>
	</a>

	<div class="p-4 flex flex-col gap-2 grow">
		<{{ $heading }} class="text-[17px] lg:text-[18px] font-bold text-ink leading-snug">
			<a href="{{ route('properties.show', $property) }}" class="hover:text-blue transition-colors">{{ $property->title }}</a>
		</{{ $heading }}>

		<p class="flex items-center gap-1 text-[13px] text-muted">
			<x-icon name="map-pin" class="w-[14px] h-[14px] shrink-0" />
			{{ $property->locationLabel() }}
		</p>

		<p class="text-[21px] font-bold text-blue">{{ $property->formattedPrice() }}</p>

		<ul class="flex flex-wrap gap-x-4 gap-y-1 text-[12px] text-muted">
			@if($property->area)
				<li class="inline-flex items-center gap-1">
					<x-icon name="area" class="w-[14px] h-[14px]" />
					{{ number_format($property->area) }} {{ $property->area_unit }}
				</li>
			@endif
			@if($property->bedrooms)
				<li class="inline-flex items-center gap-1">
					<x-icon name="bed" class="w-[14px] h-[14px]" />
					{{ $property->bedrooms }} {{ Str::plural('Bed', $property->bedrooms) }}
				</li>
			@endif
			@if($property->bathrooms)
				<li class="inline-flex items-center gap-1">
					<x-icon name="bath" class="w-[14px] h-[14px]" />
					{{ $property->bathrooms }} {{ Str::plural('Bath', $property->bathrooms) }}
				</li>
			@endif
			@if($property->furnishingLabel())
				<li class="inline-flex items-center gap-1">
					<x-icon name="sofa" class="w-[14px] h-[14px]" />
					{{ $property->furnishingLabel() }}
				</li>
			@endif
		</ul>

		<div class="flex gap-2 mt-auto pt-2">
			<a href="{{ route('properties.show', $property) }}" class="btn btn-outline flex-1 px-3">View Details</a>
			<a href="{{ $property->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-whatsapp flex-1 px-3">
				<x-icon name="whatsapp" class="w-4 h-4" />
				WhatsApp
			</a>
		</div>
	</div>
</article>
