@php
    $galleryImages = $project->images->where('type', 'gallery');
    $floorPlans = $project->images->where('type', 'floor_plan');
@endphp

<x-layouts.site
	:title="$project->seo_title ?: $project->name.' — '.$project->locationLabel().' | SP REALTORS'"
	:description="$project->seo_description ?: Str::limit(strip_tags((string) $project->description), 155)"
	:og-image="$project->heroImageUrl()"
	og-type="article">

	<div class="site-container">
		<x-breadcrumbs :items="[
			['label' => 'Projects', 'url' => route('projects.index')],
			['label' => $project->name],
		]" />
	</div>

	{{-- Project hero --}}
	<section class="site-container">
		<div class="grid gap-6 lg:grid-cols-[1fr_380px] lg:items-start">
			<div class="rounded-[10px] overflow-hidden bg-lightgray">
				@if($project->heroImageUrl())
					<img src="{{ $project->heroImageUrl() }}" alt="{{ $project->name }}"
					     class="w-full aspect-[16/9] object-cover" fetchpriority="high">
				@else
					<div class="w-full aspect-[16/9] flex items-center justify-center text-blue/30">
						<x-icon name="building" class="w-16 h-16" />
					</div>
				@endif
			</div>

			<div class="card p-5">
				<h1 class="text-[24px] lg:text-[28px] mb-1">{{ $project->name }}</h1>

				<p class="flex items-center gap-1.5 text-[14px] text-muted mb-3">
					<x-icon name="map-pin" class="w-4 h-4" />
					{{ $project->locationLabel() }}
				</p>

				<p class="text-[13px] text-muted m-0">Starting from</p>
				<p class="text-[26px] font-bold text-blue mb-4">{{ $project->formattedStartingPrice() }}</p>

				<ul class="flex flex-col gap-2 text-[13px] text-body border-t border-line pt-4">
					@if($project->configurations)
						<li class="flex items-center gap-2">
							<span class="text-blue"><x-icon name="building" class="w-4 h-4" /></span>
							{{ $project->configurations }}
						</li>
					@endif
					@if($project->possession)
						<li class="flex items-center gap-2">
							<span class="text-blue"><x-icon name="clock" class="w-4 h-4" /></span>
							Possession: {{ $project->possession }}
						</li>
					@endif
					@if($project->developer)
						<li class="flex items-center gap-2">
							<span class="text-blue"><x-icon name="users" class="w-4 h-4" /></span>
							By {{ $project->developer }}
						</li>
					@endif
					@if($project->rera_number)
						<li class="flex items-center gap-2">
							<span class="text-blue"><x-icon name="file-text" class="w-4 h-4" /></span>
							RERA: {{ $project->rera_number }}
						</li>
					@endif
				</ul>

				<div class="flex flex-col gap-2 mt-5">
					@if($project->hasBrochure())
						@if(session()->get($project->brochureUnlockKey()))
							{{-- Details already given this session — straight to the file. --}}
							<a href="{{ route('projects.brochure.download', $project) }}" class="btn btn-outline w-full">
								<x-icon name="download" class="w-[18px] h-[18px]" />
								Download Brochure
							</a>
						@else
							<button type="button" data-modal-open="brochure-modal" class="btn btn-outline w-full">
								<x-icon name="download" class="w-[18px] h-[18px]" />
								Download Brochure
							</button>
						@endif
					@endif
					<a href="#enquiry" class="btn btn-gold w-full">Enquire Now</a>
					<a href="{{ $project->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full">
						<x-icon name="whatsapp" class="w-[18px] h-[18px]" />
						WhatsApp
					</a>
				</div>
			</div>
		</div>
	</section>

	<div class="site-container pb-12">
		<div class="grid gap-8 lg:grid-cols-[1fr_380px] mt-8">
			<div>
				{{-- Overview --}}
				@if($project->description)
					<section>
						<h2 class="text-[20px] mb-3">Project Overview</h2>
						<p class="text-[14px] text-body whitespace-pre-line">{{ $project->description }}</p>
					</section>
				@endif

				{{-- Configurations --}}
				@if($project->configuration_details)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Configurations</h2>
						<div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
							@foreach($project->configuration_details as $config)
								<div class="card p-4">
									<strong class="block text-[16px] text-ink">{{ $config['type'] ?? '' }}</strong>
									@if(! empty($config['area']))
										<small class="block text-[12px] text-muted mt-0.5">
											{{ $config['area'] }}@if(! empty($config['area_type'])) · {{ $config['area_type'] }}@endif
										</small>
									@endif
									@if(! empty($config['price']))
										<p class="text-[16px] font-bold text-blue mt-2 mb-0">{{ $config['price'] }}</p>
										<small class="block text-[11px] {{ ! empty($config['all_inclusive']) ? 'text-green-dark font-semibold' : 'text-muted' }}">
											{{ ! empty($config['all_inclusive']) ? 'All inclusive' : 'Excl. taxes & charges' }}
										</small>
									@endif
								</div>
							@endforeach
						</div>
					</section>
				@endif

				{{-- Highlights --}}
				@if($project->highlights)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Project Highlights</h2>
						<ul class="grid sm:grid-cols-2 gap-2">
							@foreach($project->highlights as $highlight)
								<li class="flex items-center gap-2 text-[14px] text-body">
									<span class="text-green shrink-0"><x-icon name="check" class="w-[18px] h-[18px]" /></span>
									{{ $highlight }}
								</li>
							@endforeach
						</ul>
					</section>
				@endif

				{{-- Amenities --}}
				@if($project->amenities)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Amenities</h2>
						<ul class="grid grid-cols-2 sm:grid-cols-3 gap-4">
							@foreach($project->amenities as $amenity)
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

				{{-- Gallery --}}
				@if($galleryImages->isNotEmpty())
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Gallery</h2>
						<div class="flex gap-3 overflow-x-auto no-scrollbar snap-x">
							@foreach($galleryImages as $image)
								<img src="{{ $image->url() }}" alt="{{ $image->alt ?: $project->name }}" loading="lazy"
								     class="shrink-0 w-[260px] aspect-[4/3] object-cover rounded-[8px] snap-start">
							@endforeach
						</div>
					</section>
				@endif

				{{-- Floor plans --}}
				@if($floorPlans->isNotEmpty())
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Floor Plans</h2>
						<div class="flex gap-3 overflow-x-auto no-scrollbar snap-x">
							@foreach($floorPlans as $plan)
								<figure class="shrink-0 w-[260px] snap-start m-0">
									<img src="{{ $plan->url() }}" alt="{{ $plan->alt ?: 'Floor plan' }}" loading="lazy"
									     class="w-full aspect-[4/3] object-cover rounded-[8px] border border-line bg-white">
									@if($plan->label)
										<figcaption class="text-[12px] text-muted mt-1 text-center">{{ $plan->label }}</figcaption>
									@endif
								</figure>
							@endforeach
						</div>
					</section>
				@endif

				{{-- Location + nearby --}}
				@if($project->map_url || $project->nearby_places)
					<section class="mt-8">
						<h2 class="text-[20px] mb-3">Location</h2>
						<div class="grid gap-4 md:grid-cols-[1fr_280px]">
							@if($project->map_url)
								<div class="rounded-[8px] overflow-hidden aspect-[16/10] bg-lightgray">
									<iframe src="{{ $project->map_url }}" title="Map of {{ $project->name }}"
									        class="w-full h-full border-0" loading="lazy"
									        referrerpolicy="no-referrer-when-downgrade"></iframe>
								</div>
							@endif

							@if($project->nearby_places)
								<div class="card p-4">
									<strong class="block text-[14px] text-ink mb-2">Nearby Places</strong>
									<ul class="flex flex-col gap-1.5">
										@foreach($project->nearby_places as $place)
											<li class="flex items-start gap-2 text-[13px] text-body">
												<span class="text-blue shrink-0 mt-0.5"><x-icon name="map-pin" class="w-4 h-4" /></span>
												{{ $place }}
											</li>
										@endforeach
									</ul>
								</div>
							@endif
						</div>
					</section>
				@endif

				{{-- Related --}}
				@if($related->isNotEmpty())
					<section class="mt-10">
						<div class="flex items-end justify-between gap-4 mb-5">
							<h2 class="text-[20px] lg:text-[24px]">Related Projects</h2>
							<a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-blue hover:underline shrink-0">
								View All Projects <x-icon name="arrow-right" class="w-4 h-4" />
							</a>
						</div>
						<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
							@foreach($related as $item)
								<x-project-card :project="$item" />
							@endforeach
						</div>
					</section>
				@endif
			</div>

			{{-- Enquiry --}}
			<aside>
				<div class="card p-5 lg:sticky lg:top-24">
					<x-enquiry-form
						source="project"
						:project="$project"
						title="Enquire About This Project"
						button-label="Send Enquiry" />
				</div>
			</aside>
		</div>
	</div>

	{{-- Details just submitted: pull the file without navigating away. --}}
	@if(session('brochure_download_url'))
		<iframe src="{{ session('brochure_download_url') }}" class="hidden" title="Brochure download" aria-hidden="true"></iframe>
	@endif

	{{-- Brochure is only released after the visitor shares their details. --}}
	@if($project->hasBrochure() && ! session()->get($project->brochureUnlockKey()))
		<x-lead-modal
			id="brochure-modal"
			:action="route('projects.brochure.request', $project)"
			source="brochure"
			title="Download the brochure"
			:text="'Tell us where to reach you and the '.$project->name.' brochure will download right away.'"
			button-label="Download Brochure" />
	@endif

	<x-slot:sticky-cta>
		<x-mobile-sticky-cta
			:whatsapp-url="$project->whatsappUrl()"
			secondary-label="Enquire"
			secondary-href="#enquiry" />
	</x-slot:sticky-cta>
</x-layouts.site>
