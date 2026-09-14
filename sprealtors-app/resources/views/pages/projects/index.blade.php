<x-layouts.site
	title="New Projects in Navi Mumbai | SP REALTORS"
	description="Discover premium residential and commercial projects in Navi Mumbai by trusted developers — Kharghar, Panvel, Ulwe, Vashi and more.">

	<x-page-hero
		heading="Projects"
		text="Discover premium residential and commercial projects in Navi Mumbai by trusted developers."
		:image="asset('images/hero-building.webp')"
		compact />

	<div class="site-container">
		<x-breadcrumbs :items="[['label' => 'Projects']]" />
	</div>

	<section class="site-container pb-12">
		{{-- Filter bar --}}
		<form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap items-end gap-3 mb-6">
			<div class="w-full sm:w-auto">
				<label for="project-location" class="field-label">Location</label>
				<select id="project-location" name="location" class="field sm:w-[200px]" data-sort-select>
					<option value="">All locations</option>
					@foreach($locations as $location)
						<option value="{{ $location->slug }}" @selected(request('location') === $location->slug)>{{ $location->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="w-full sm:w-auto">
				<label for="project-status" class="field-label">Status</label>
				<select id="project-status" name="status" class="field sm:w-[200px]" data-sort-select>
					<option value="">All status</option>
					@foreach(\App\Models\Project::statusOptions() as $value => $label)
						<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
					@endforeach
				</select>
			</div>

			<noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>

			@if(request()->hasAny(['location', 'status']))
				<a href="{{ route('projects.index') }}" class="text-[13px] font-semibold text-blue hover:underline pb-3">Clear</a>
			@endif
		</form>

		@if($projects->isEmpty())
			<div class="card p-10 text-center">
				<span class="inline-flex text-blue/40 mb-3"><x-icon name="building" class="w-10 h-10" /></span>
				<h2 class="text-[20px] mb-2">No projects found</h2>
				<p class="text-[14px] text-muted mb-4">Try a different location or status.</p>
				<a href="{{ route('projects.index') }}" class="btn btn-primary inline-flex">View All Projects</a>
			</div>
		@else
			<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
				@foreach($projects as $project)
					<x-project-card :project="$project" heading="h2" />
				@endforeach
			</div>

			<div class="mt-8">
				{{ $projects->links('pagination.default') }}
			</div>
		@endif
	</section>

	<x-cta-banner />
</x-layouts.site>
