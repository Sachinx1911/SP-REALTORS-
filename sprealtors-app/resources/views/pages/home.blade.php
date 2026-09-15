<x-layouts.site>
	{{-- 2. HERO --------------------------------------------------------- --}}
	<section class="relative bg-lightgray overflow-hidden">
		{{-- Background image (desktop full-bleed) --}}
		<div class="absolute inset-0 hidden lg:block">
			<picture>
				<source srcset="{{ asset('images/hero-building.webp') }}" type="image/webp">
				<img src="{{ asset('images/hero-building.jpg') }}" alt=""
				     class="w-full h-full object-cover object-center" fetchpriority="high" width="1920" height="650">
			</picture>
			<div class="absolute inset-0 bg-[linear-gradient(100deg,rgba(244,246,248,.92)_0%,rgba(244,246,248,.6)_34%,rgba(244,246,248,.1)_58%,rgba(244,246,248,0)_70%)]"></div>
		</div>

		<div class="site-container relative py-10 lg:py-0 lg:min-h-[430px] lg:flex lg:flex-col lg:justify-center lg:gap-6">
			{{-- Text --}}
			<div class="lg:max-w-[620px]">
				<span class="eyebrow">Find a Better Tomorrow</span>
				<h1 class="mt-3 text-[32px] sm:text-[40px] lg:text-[52px] leading-[1.08]">
					Trusted Real Estate<br class="hidden sm:block"> Partner in Navi Mumbai
				</h1>
				<p class="mt-3 text-[15px] lg:text-[17px] text-ink max-w-[46ch]">
					Buy | Rent | Invest — Residential &amp; Commercial Properties with expert guidance.
				</p>
			</div>

			{{-- Mobile image --}}
			<div class="relative mt-6 lg:hidden rounded-[10px] overflow-hidden">
				<picture>
					<source srcset="{{ asset('images/hero-building.webp') }}" type="image/webp">
					<img src="{{ asset('images/hero-building.jpg') }}" alt="Modern residential tower in Navi Mumbai"
					     class="w-full aspect-[16/10] object-cover" fetchpriority="high">
				</picture>
				<span class="absolute top-3 right-3 bg-white/92 text-navy font-display italic text-[13px] leading-tight text-right px-3 py-2 rounded-[6px] shadow-[var(--shadow-soft)] max-w-[60%]">
					A Better Tomorrow Starts Here
				</span>
			</div>

			{{-- Desktop cursive badge --}}
			<span class="hidden lg:block absolute top-8 right-20 font-display italic text-navy text-[19px] leading-snug text-right max-w-[220px]">
				A Better Tomorrow Starts Here
				<span class="block w-4/5 ml-auto h-[3px] bg-gold rounded-full mt-1 -rotate-3"></span>
			</span>

			{{-- Search --}}
			<x-search-form :locations="$filterLocations" class="mt-6 lg:mt-0 lg:max-w-[900px]" />
		</div>
	</section>

	{{-- 3. TRUST BAR ---------------------------------------------------- --}}
	<section class="border-b border-line bg-white">
		<div class="site-container py-5 lg:py-0">
			<ul class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:h-16 lg:items-center">
				@foreach([
					['check-circle', 'Verified Properties'],
					['settings', 'Local Expertise'],
					['file-text', 'Transparent Process'],
					['headset', 'End-to-End Assistance'],
				] as [$icon, $label])
					<li class="flex items-center gap-2.5 text-[12px] lg:text-[13px] font-semibold text-ink">
						<span class="text-blue shrink-0"><x-icon :name="$icon" class="w-[22px] h-[22px]" /></span>
						{{ $label }}
					</li>
				@endforeach
			</ul>
		</div>
	</section>

	{{-- 4. FEATURED PROPERTIES ------------------------------------------ --}}
	@if($featured->isNotEmpty())
		<section class="section">
			<div class="site-container">
				<div class="flex items-end justify-between gap-4 mb-6">
					<div>
						<h2 class="text-[24px] lg:text-[30px]">Featured Properties</h2>
						<p class="text-[13px] text-muted m-0">Handpicked listings for you</p>
					</div>
					<a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-blue hover:underline shrink-0">
						View All Properties <x-icon name="arrow-right" class="w-4 h-4" />
					</a>
				</div>

				<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
					@foreach($featured as $property)
						<x-property-card :property="$property" />
					@endforeach
				</div>
			</div>
		</section>
	@endif

	{{-- 5. FEATURED PROJECTS -------------------------------------------- --}}
	@if($featuredProjects->isNotEmpty())
		<section class="section pt-0">
			<div class="site-container">
				<div class="flex items-end justify-between gap-4 mb-6">
					<div>
						<h2 class="text-[24px] lg:text-[30px]">New Projects</h2>
						<p class="text-[13px] text-muted m-0">Premium projects by trusted developers</p>
					</div>
					<a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-blue hover:underline shrink-0">
						View All Projects <x-icon name="arrow-right" class="w-4 h-4" />
					</a>
				</div>

				<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
					@foreach($featuredProjects as $project)
						<x-project-card :project="$project" />
					@endforeach
				</div>
			</div>
		</section>
	@endif

	{{-- 6. BROWSE BY REQUIREMENT ---------------------------------------- --}}
	<section class="section pt-0">
		<div class="site-container">
			<h2 class="text-[24px] lg:text-[30px] mb-5">Browse by Requirement</h2>

			<div class="grid grid-cols-2 md:grid-cols-4 gap-3 lg:gap-4">
				@foreach([
					['home', 'Buy Property', 'Find your dream home', ['purpose' => 'buy']],
					['key', 'Rent Property', 'Homes &amp; offices for rent', ['purpose' => 'rent']],
					['building', 'Residential', 'Apartments, Villas &amp; More', ['type' => ['residential']]],
					['store', 'Commercial', 'Office, Shop, Showroom &amp; More', ['type' => ['commercial']]],
				] as [$icon, $title, $text, $query])
					<a href="{{ route('properties.index', $query) }}"
					   class="card card-hover flex items-center gap-3 p-4 min-h-[105px] lg:min-h-[110px]">
						<span class="text-blue shrink-0"><x-icon :name="$icon" class="w-8 h-8" /></span>
						<span>
							<strong class="block text-[14px] text-ink">{{ $title }}</strong>
							<small class="block text-[11px] lg:text-[12px] text-muted leading-snug">{!! $text !!}</small>
						</span>
					</a>
				@endforeach
			</div>
		</div>
	</section>

	{{-- 6. AREAS WE SERVE ----------------------------------------------- --}}
	@if($locations->isNotEmpty())
		<section class="section pt-0">
			<div class="site-container">
				<div class="flex items-end justify-between gap-4 mb-5">
					<h2 class="text-[24px] lg:text-[30px]">Areas We Serve</h2>
					<a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-blue hover:underline shrink-0">
						Explore Properties by Location <x-icon name="arrow-right" class="w-4 h-4" />
					</a>
				</div>

				<div class="flex gap-2.5 overflow-x-auto no-scrollbar snap-x lg:grid lg:grid-cols-8">
					@foreach($locations as $location)
						<a href="{{ route('properties.index', ['location' => [$location->slug]]) }}"
						   class="relative shrink-0 w-[140px] lg:w-auto aspect-[150/105] rounded-[8px] overflow-hidden snap-start group">
							@if($location->imageUrl())
								<img src="{{ $location->imageUrl() }}" alt="" loading="lazy"
								     class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
							@else
								<span class="w-full h-full block bg-gradient-to-br from-[#cfe0f0] to-[#7fa6ca]"></span>
							@endif
							<span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy/80 to-transparent text-white text-[13px] font-bold text-center py-2">
								{{ $location->name }}
							</span>
						</a>
					@endforeach
				</div>
			</div>
		</section>
	@endif

	{{-- 7. WHY CHOOSE SP REALTORS --------------------------------------- --}}
	<section class="bg-gold-bg">
		<div class="site-container py-9 lg:py-10">
			<div class="grid gap-6 lg:grid-cols-[340px_1fr] lg:items-center">
				<div>
					<span class="eyebrow">Why Choose SP REALTORS</span>
					<h2 class="mt-2 text-[26px] lg:text-[34px] leading-tight">A Smoother, Smarter Property Experience</h2>
				</div>

				<ul class="grid grid-cols-2 md:grid-cols-5 gap-5 text-center">
					@foreach([
						['check-circle', 'Verified Listings'],
						['trending-up', 'Local Market Knowledge'],
						['file-text', 'Transparent Guidance'],
						['users', 'Negotiation Support'],
						['headset', 'Complete Assistance'],
					] as [$icon, $label])
						<li>
							<span class="text-blue inline-block mb-2"><x-icon :name="$icon" class="w-7 h-7 mx-auto" /></span>
							<strong class="block text-[13px] text-ink leading-snug">{{ $label }}</strong>
						</li>
					@endforeach
				</ul>
			</div>
		</div>
	</section>

	{{-- 8. TESTIMONIALS -------------------------------------------------- --}}
	@if($testimonials->isNotEmpty())
		<section class="section">
			<div class="site-container">
				<div class="flex items-end justify-between gap-4 mb-6">
					<h2 class="text-[24px] lg:text-[30px]">What Our Clients Say</h2>
					<p class="text-[13px] text-muted m-0 shrink-0">Real experiences. Real trust.</p>
				</div>

				<div class="grid gap-5 md:grid-cols-3">
					@foreach($testimonials as $testimonial)
						<x-testimonial-card :testimonial="$testimonial" />
					@endforeach
				</div>
			</div>
		</section>
	@endif

	{{-- 9. CTA ----------------------------------------------------------- --}}
	<x-cta-banner />
</x-layouts.site>
