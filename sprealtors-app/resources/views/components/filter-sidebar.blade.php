@props([
    'filters' => [],
    'sort' => null,
    'locations' => collect(),
    'typeCounts' => [],
    'purposeCounts' => [],
    'configurationCounts' => [],
    'budgetCounts' => [],
])

<form method="GET" action="{{ route('properties.index') }}" data-filter-form {{ $attributes }}>
	@if($sort)
		<input type="hidden" name="sort" value="{{ $sort }}">
	@endif

	<div class="flex items-center justify-between mb-5">
		<h2 class="font-sans text-[16px] font-bold text-ink m-0">Filters</h2>
		<a href="{{ route('properties.index') }}" class="text-[12px] font-semibold text-blue hover:underline">Clear All</a>
	</div>

	{{-- Property Type --}}
	<fieldset class="mb-6">
		<legend class="text-[13px] font-bold text-ink mb-2">Property Type</legend>
		@foreach(['residential' => 'Residential', 'commercial' => 'Commercial'] as $value => $label)
			<label class="flex items-center justify-between gap-2 py-1.5 text-[13px] text-body cursor-pointer">
				<span class="flex items-center gap-2">
					<input type="checkbox" name="type[]" value="{{ $value }}"
					       class="w-4 h-4 accent-blue"
					       @checked(in_array($value, (array) ($filters['type'] ?? []), true))>
					{{ $label }}
				</span>
				<span class="text-[12px] text-muted">({{ $typeCounts[$value] ?? 0 }})</span>
			</label>
		@endforeach
	</fieldset>

	{{-- Purpose --}}
	<fieldset class="mb-6">
		<legend class="text-[13px] font-bold text-ink mb-2">Purpose</legend>
		@foreach(['buy' => 'Buy', 'rent' => 'Rent'] as $value => $label)
			<label class="flex items-center justify-between gap-2 py-1.5 text-[13px] text-body cursor-pointer">
				<span class="flex items-center gap-2">
					<input type="radio" name="purpose" value="{{ $value }}"
					       class="w-4 h-4 accent-blue"
					       @checked(($filters['purpose'] ?? null) === $value)>
					{{ $label }}
				</span>
				<span class="text-[12px] text-muted">({{ $purposeCounts[$value] ?? 0 }})</span>
			</label>
		@endforeach
	</fieldset>

	{{-- Configuration --}}
	<fieldset class="mb-6">
		<legend class="text-[13px] font-bold text-ink mb-2">Configuration</legend>
		@foreach(\App\Models\Property::configurationOptions() as $value => $label)
			@continue(($configurationCounts[$value] ?? 0) === 0 && ! in_array($value, (array) ($filters['configuration'] ?? []), true))
			<label class="flex items-center justify-between gap-2 py-1.5 text-[13px] text-body cursor-pointer">
				<span class="flex items-center gap-2">
					<input type="checkbox" name="configuration[]" value="{{ $value }}"
					       class="w-4 h-4 accent-blue"
					       @checked(in_array($value, (array) ($filters['configuration'] ?? []), true))>
					{{ $label }}
				</span>
				<span class="text-[12px] text-muted">({{ $configurationCounts[$value] ?? 0 }})</span>
			</label>
		@endforeach
	</fieldset>

	{{-- Budget --}}
	<fieldset class="mb-6">
		<legend class="text-[13px] font-bold text-ink mb-2">Budget</legend>
		@foreach(\App\Models\Property::budgetOptions() as $value => $label)
			<label class="flex items-center justify-between gap-2 py-1.5 text-[13px] text-body cursor-pointer">
				<span class="flex items-center gap-2">
					<input type="checkbox" name="budget[]" value="{{ $value }}"
					       class="w-4 h-4 accent-blue"
					       @checked(in_array($value, (array) ($filters['budget'] ?? []), true))>
					{{ $label }}
				</span>
				<span class="text-[12px] text-muted">({{ $budgetCounts[$value] ?? 0 }})</span>
			</label>
		@endforeach
	</fieldset>

	{{-- Location --}}
	<fieldset class="mb-6">
		<legend class="text-[13px] font-bold text-ink mb-2">Location</legend>
		@foreach($locations as $location)
			<label class="flex items-center justify-between gap-2 py-1.5 text-[13px] text-body cursor-pointer">
				<span class="flex items-center gap-2">
					<input type="checkbox" name="location[]" value="{{ $location->slug }}"
					       class="w-4 h-4 accent-blue"
					       @checked(in_array($location->slug, (array) ($filters['location'] ?? []), true))>
					{{ $location->name }}
				</span>
				<span class="text-[12px] text-muted">({{ $location->properties_count ?? 0 }})</span>
			</label>
		@endforeach
	</fieldset>

	<noscript>
		<button type="submit" class="btn btn-primary w-full">Apply Filters</button>
	</noscript>
</form>
