@props(['locations' => collect(), 'compact' => false])

@php
    $purpose = request('purpose', 'buy');
@endphp

<form action="{{ route('properties.index') }}" method="GET"
      {{ $attributes->merge(['class' => 'bg-white rounded-[10px] shadow-[0_16px_40px_rgba(9,43,80,0.14)] p-4 lg:p-5']) }}>

	{{-- Buy / Rent --}}
	<div class="inline-flex bg-lightgray rounded-full p-1 mb-4">
		@foreach(['buy' => 'Buy', 'rent' => 'Rent'] as $value => $label)
			<label class="relative cursor-pointer">
				<input type="radio" name="purpose" value="{{ $value }}" class="sr-only peer"
				       @checked($purpose === $value)>
				<span class="inline-flex items-center px-6 py-2 rounded-full text-[14px] font-bold text-navy
				             peer-checked:bg-navy peer-checked:text-white transition-colors">
					{{ $label }}
				</span>
			</label>
		@endforeach
	</div>

	<div class="grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto] md:items-end">
		<div>
			<label for="search-location" class="field-label">Location</label>
			<div class="relative">
				<span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue pointer-events-none">
					<x-icon name="map-pin" class="w-4 h-4" />
				</span>
				<select id="search-location" name="location[]" class="field pl-9">
					<option value="">All locations</option>
					@foreach($locations as $location)
						<option value="{{ $location->slug }}" @selected(in_array($location->slug, (array) request('location', []), true))>
							{{ $location->name }}
						</option>
					@endforeach
				</select>
			</div>
		</div>

		<div>
			<label for="search-type" class="field-label">Property Type</label>
			<div class="relative">
				<span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue pointer-events-none">
					<x-icon name="building" class="w-4 h-4" />
				</span>
				<select id="search-type" name="type[]" class="field pl-9">
					<option value="">All types</option>
					<option value="residential" @selected(in_array('residential', (array) request('type', []), true))>Residential</option>
					<option value="commercial" @selected(in_array('commercial', (array) request('type', []), true))>Commercial</option>
				</select>
			</div>
		</div>

		<div>
			<label for="search-budget" class="field-label">Budget</label>
			<div class="relative">
				<span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue pointer-events-none">
					<x-icon name="key" class="w-4 h-4" />
				</span>
				<select id="search-budget" name="budget[]" class="field pl-9">
					<option value="">Any budget</option>
					@foreach(\App\Models\Property::budgetOptions() as $value => $label)
						<option value="{{ $value }}" @selected(in_array($value, (array) request('budget', []), true))>{{ $label }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<button type="submit" class="btn btn-gold w-full md:w-auto md:px-7 h-[46px]">
			<x-icon name="search" class="w-[18px] h-[18px]" />
			Search
		</button>
	</div>
</form>
