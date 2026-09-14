<x-layouts.site
	title="Properties for Sale &amp; Rent in Navi Mumbai | SP REALTORS"
	description="Browse verified residential and commercial properties for sale and rent across Kharghar, Panvel, Vashi, Nerul, Ulwe and more in Navi Mumbai.">

	<x-page-hero
		heading="Properties"
		text="Explore residential and commercial properties in Navi Mumbai and nearby areas."
		:image="asset('images/hero-building.webp')"
		compact />

	<div class="site-container">
		<x-breadcrumbs :items="[['label' => 'Properties']]" />
	</div>

	<section class="site-container pb-12">
		<div class="grid gap-6 lg:grid-cols-[240px_1fr]">

			{{-- Filters --}}
			<div>
				<button type="button"
				        class="btn btn-outline w-full lg:hidden mb-4"
				        data-filter-toggle aria-expanded="false" aria-controls="filter-panel">
					<x-icon name="filter" class="w-[18px] h-[18px]" />
					Filters
				</button>

				<aside id="filter-panel" class="hidden lg:block" data-filter-panel>
					<x-filter-sidebar
						:filters="$filters"
						:sort="$sort"
						:locations="$locations"
						:type-counts="$typeCounts"
						:purpose-counts="$purposeCounts"
						:configuration-counts="$configurationCounts"
						:budget-counts="$budgetCounts"
						class="card p-5 lg:border-0 lg:shadow-none lg:p-0" />
				</aside>
			</div>

			{{-- Results --}}
			<div>
				<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
					<p class="text-[13px] text-muted m-0">
						Showing {{ $properties->total() }} {{ Str::plural('property', $properties->total()) }}
					</p>

					<form method="GET" action="{{ route('properties.index') }}" class="flex items-center gap-2">
						@foreach(['purpose' => $filters['purpose'] ?? null] as $key => $value)
							@if($value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
						@endforeach
						@foreach(['type', 'configuration', 'location', 'budget'] as $key)
							@foreach((array) ($filters[$key] ?? []) as $value)
								<input type="hidden" name="{{ $key }}[]" value="{{ $value }}">
							@endforeach
						@endforeach

						<label for="sort" class="text-[13px] text-muted whitespace-nowrap">Sort by:</label>
						<select id="sort" name="sort" class="field h-[38px] w-auto text-[13px] py-0" data-sort-select>
							<option value="latest" @selected($sort === 'latest' || ! $sort)>Latest</option>
							<option value="price_low" @selected($sort === 'price_low')>Price: Low to High</option>
							<option value="price_high" @selected($sort === 'price_high')>Price: High to Low</option>
						</select>
						<noscript><button type="submit" class="btn btn-outline h-[38px]">Go</button></noscript>
					</form>
				</div>

				@if($properties->isEmpty())
					<div class="card p-10 text-center">
						<span class="inline-flex text-blue/40 mb-3"><x-icon name="search" class="w-10 h-10" /></span>
						<h2 class="text-[20px] mb-2">No properties found</h2>
						<p class="text-[14px] text-muted mb-4">Try adjusting or clearing your filters.</p>
						<a href="{{ route('properties.index') }}" class="btn btn-primary inline-flex">Clear Filters</a>
					</div>
				@else
					<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
						@foreach($properties as $property)
							<x-property-card :property="$property" heading="h2" />
						@endforeach
					</div>

					<div class="mt-8">
						{{ $properties->links('pagination.default') }}
					</div>
				@endif
			</div>
		</div>
	</section>

	<x-cta-banner />
</x-layouts.site>
