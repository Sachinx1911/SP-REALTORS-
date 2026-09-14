<x-layouts.site
	title="About SP REALTORS — Your Trusted Real Estate Partner in Navi Mumbai"
	description="SP REALTORS is a Navi Mumbai based real estate advisory and brokerage firm helping buyers, sellers, investors and tenants with transparent, end-to-end support.">

	<x-page-hero
		eyebrow="About SP REALTORS"
		heading="Your Trusted Real Estate Partner"
		text="At SP REALTORS, we believe that finding the right property is more than just a transaction — it's a step towards a better future. We help buyers, sellers, investors and tenants with reliable guidance, market expertise and transparent service."
		badge="Good Spaces Better Lives"
		:image="asset('images/hero-building.webp')">
		<a href="{{ route('properties.index') }}" class="btn btn-primary mt-5 inline-flex">
			Our Properties
			<x-icon name="arrow-right" class="w-[18px] h-[18px]" />
		</a>
	</x-page-hero>

	{{-- Stats --}}
	@if($stats)
		<section class="border-b border-line">
			<div class="site-container py-8">
				<ul class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
					@foreach($stats as $i => $stat)
						<li>
							<span class="text-blue inline-block mb-2">
								<x-icon :name="['users', 'home', 'award', 'map-pin'][$i] ?? 'check-circle'" class="w-6 h-6 mx-auto" />
							</span>
							<strong class="block font-display text-[26px] lg:text-[30px] text-navy leading-tight">{{ $stat['number'] }}</strong>
							<span class="block text-[12px] text-muted">{{ $stat['label'] }}</span>
						</li>
					@endforeach
				</ul>
			</div>
		</section>
	@endif

	{{-- Who we are --}}
	<section class="section">
		<div class="site-container">
			<div class="grid gap-8 lg:grid-cols-2 lg:items-center lg:gap-12">
				<div class="rounded-[10px] overflow-hidden bg-lightgray order-2 lg:order-1">
					<picture>
						<source srcset="{{ asset('images/hero-building.webp') }}" type="image/webp">
						<img src="{{ asset('images/hero-building.jpg') }}" alt="Residential buildings in Navi Mumbai"
						     loading="lazy" class="w-full aspect-[4/3] object-cover">
					</picture>
				</div>

				<div class="order-1 lg:order-2">
					<span class="eyebrow">Who We Are</span>
					<h2 class="mt-2 text-[26px] lg:text-[32px]">Local Expertise. Real Solutions.</h2>
					<p class="mt-3 text-[14px] lg:text-[15px] text-body">
						SP REALTORS is a Navi Mumbai based real estate advisory and brokerage firm committed to making
						property transactions simple, transparent and rewarding. With deep local knowledge and a
						client-first approach, we provide end-to-end support for all your real estate needs —
						residential, commercial, buying, selling, renting or investing.
					</p>

					<ul class="grid grid-cols-2 gap-3 mt-5">
						@foreach([
							'Verified Properties', 'Transparent Guidance',
							'Local Market Knowledge', 'End-to-End Assistance',
						] as $item)
							<li class="flex items-center gap-2 text-[13px] font-semibold text-ink">
								<span class="text-green shrink-0"><x-icon name="check-circle" class="w-[18px] h-[18px]" /></span>
								{{ $item }}
							</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
	</section>

	{{-- Our approach --}}
	<section class="section pt-0">
		<div class="site-container">
			<div class="mb-6">
				<h2 class="text-[24px] lg:text-[30px]">Our Approach</h2>
				<p class="text-[13px] text-muted m-0">A simple, transparent process to help you find the right property.</p>
			</div>

			<ol class="grid gap-4 md:grid-cols-4">
				@foreach([
					['Understand Your Needs', 'We listen to your requirements and goals.', 'headset'],
					['Share Best Options', 'We shortlist the most suitable properties.', 'home'],
					['Site Visits & Comparisons', 'We arrange site visits and help you compare.', 'building'],
					['Support Till Final Deal', 'We assist with negotiation and documentation.', 'handshake'],
				] as $i => [$title, $text, $icon])
					<li class="card p-5">
						<div class="flex items-center justify-between mb-3">
							<span class="w-8 h-8 rounded-full bg-gold text-white text-[13px] font-bold flex items-center justify-center">
								{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
							</span>
							<span class="text-blue"><x-icon :name="$icon" class="w-5 h-5" /></span>
						</div>
						<strong class="block text-[14px] text-ink mb-1">{{ $title }}</strong>
						<small class="block text-[12px] text-muted leading-snug">{{ $text }}</small>
					</li>
				@endforeach
			</ol>
		</div>
	</section>

	{{-- Why choose --}}
	<section class="bg-gold-bg">
		<div class="site-container py-9 lg:py-10">
			<h2 class="text-[24px] lg:text-[30px] mb-6">Why Choose SP REALTORS?</h2>
			<ul class="grid grid-cols-2 md:grid-cols-5 gap-5 text-center">
				@foreach([
					['check-circle', 'Verified Listings', 'Genuine properties'],
					['trending-up', 'Local Market Knowledge', 'In-depth expertise'],
					['file-text', 'Transparent Guidance', 'Clear and honest advice'],
					['users', 'Negotiation Support', 'Better decisions'],
					['headset', 'Complete Assistance', 'From search to final deal'],
				] as [$icon, $title, $text])
					<li>
						<span class="text-blue inline-block mb-2"><x-icon :name="$icon" class="w-7 h-7 mx-auto" /></span>
						<strong class="block text-[13px] text-ink leading-snug">{{ $title }}</strong>
						<small class="block text-[11px] text-muted mt-0.5">{{ $text }}</small>
					</li>
				@endforeach
			</ul>
		</div>
	</section>

	<x-cta-banner
		heading="Let's Find the Right Property Together"
		text="Get in touch with us today and take the next step towards your property goals." />
</x-layouts.site>
