@props(['project', 'heading' => 'h3'])

<article class="card card-hover overflow-hidden flex flex-col">
	<a href="{{ route('projects.show', $project) }}" class="relative block aspect-[413/220] bg-lightblue" tabindex="-1" aria-hidden="true">
		@if($project->heroImageUrl())
			<img src="{{ $project->heroImageUrl() }}" alt="" loading="lazy" class="w-full h-full object-cover">
		@else
			<span class="w-full h-full flex items-center justify-center text-blue/40">
				<x-icon name="building" class="w-12 h-12" />
			</span>
		@endif

		@if($project->statusLabel())
			<span class="badge badge-sale absolute top-3 left-3">{{ $project->statusLabel() }}</span>
		@endif
	</a>

	<div class="p-4 flex flex-col gap-2 grow">
		<{{ $heading }} class="text-[17px] lg:text-[18px] font-bold text-ink leading-snug">
			<a href="{{ route('projects.show', $project) }}" class="hover:text-blue transition-colors">{{ $project->name }}</a>
		</{{ $heading }}>

		<p class="flex items-center gap-1 text-[13px] text-muted">
			<x-icon name="map-pin" class="w-[14px] h-[14px] shrink-0" />
			{{ $project->locationLabel() }}
		</p>

		<p class="text-[21px] font-bold text-blue">{{ $project->formattedStartingPrice() }}</p>

		<ul class="flex flex-col gap-1 text-[12px] text-muted">
			@if($project->configurations)
				<li class="inline-flex items-center gap-1">
					<x-icon name="building" class="w-[14px] h-[14px]" />
					{{ $project->configurations }}
				</li>
			@endif
			@if($project->possession)
				<li class="inline-flex items-center gap-1">
					<x-icon name="clock" class="w-[14px] h-[14px]" />
					Possession: {{ $project->possession }}
				</li>
			@endif
		</ul>

		<div class="flex gap-2 mt-auto pt-2">
			<a href="{{ route('projects.show', $project) }}" class="btn btn-outline flex-1 px-3">View Details</a>
			<a href="{{ route('projects.show', $project) }}#enquiry" class="btn btn-gold flex-1 px-3">Enquire Now</a>
		</div>
	</div>
</article>
