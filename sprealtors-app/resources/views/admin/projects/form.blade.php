@php
    $isEdit = $project->exists;
    $oldInclusive = array_map('strval', (array) old('config_all_inclusive', []));

    $configs = old('config_type')
        ? collect(old('config_type'))->map(fn ($t, $i) => [
            'type' => $t,
            'area' => old('config_area')[$i] ?? '',
            'area_type' => old('config_area_type')[$i] ?? '',
            'price' => old('config_price')[$i] ?? '',
            'all_inclusive' => in_array((string) $i, $oldInclusive, true),
        ])->all()
        : ($project->configuration_details ?? []);

    $blankRow = ['type' => '', 'area' => '', 'area_type' => '', 'price' => '', 'all_inclusive' => false];
    // Always render a few blank rows so new entries can be added.
    $configRows = array_pad($configs, max(count($configs) + 2, 4), $blankRow);

    // Dropdown choices are managed in Admin → Settings.
    $unitTypes = \App\Models\Setting::list('config_unit_types');
    $areaTypes = \App\Models\Setting::list('config_area_types');
@endphp

<x-layouts.admin
	:title="$isEdit ? 'Edit Project' : 'Add Project'"
	:subtitle="$isEdit ? $project->name : 'Create a new project.'">

	<x-slot:actions>
		<a href="{{ route('admin.projects.index') }}" class="btn btn-outline">Back to list</a>
	</x-slot:actions>

	<form method="POST"
	      action="{{ $isEdit ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
	      enctype="multipart/form-data"
	      class="grid gap-5 lg:grid-cols-[1fr_320px] items-start">
		@csrf
		@if($isEdit) @method('PUT') @endif

		<div class="flex flex-col gap-5 min-w-0">

			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Basic Details</h2>
				<div class="grid gap-4 sm:grid-cols-2">
					<x-admin.field name="name" label="Project Name" :value="$project->name" required class="sm:col-span-2" />
					<x-admin.field name="slug" label="Slug" :value="$project->slug"
					               placeholder="auto-generated from name" help="URL: /project/your-slug" class="sm:col-span-2" />

					<x-admin.field name="developer" label="Developer" :value="$project->developer" />
					<x-admin.field name="location_id" label="Location" type="select"
					               :value="$project->location_id" placeholder="— Select —"
					               :options="$locations->pluck('name', 'id')" />

					<x-admin.field name="property_type" label="Project Type" type="select" required
					               :value="$project->property_type"
					               :options="['residential' => 'Residential', 'commercial' => 'Commercial']" />

					<x-admin.field name="status" label="Status" type="select" required
					               :value="$project->status" :options="\App\Models\Project::statusOptions()" />

					<x-admin.field name="starting_price" label="Starting Price (₹)" type="number" min="0"
					               :value="$project->starting_price ? (int) $project->starting_price : null"
					               help="Full amount, e.g. 12500000" />
					<x-admin.field name="configurations" label="Configurations Summary" :value="$project->configurations"
					               placeholder="e.g. 2, 3 &amp; 4 BHK Apartments" />

					<x-admin.field name="possession" label="Possession" :value="$project->possession" placeholder="e.g. Dec 2026" />
					<x-admin.field name="rera_number" label="RERA Number" :value="$project->rera_number" />

					<x-admin.field name="address" label="Address" :value="$project->address" class="sm:col-span-2" />
				</div>
			</section>

			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Configurations</h2>
				<p class="text-[12px] text-muted mb-3">
					One row per unit type. Leave a row blank to skip it.
					Add more Type / Area Type choices in
					<a href="{{ route('admin.settings.edit') }}" class="text-blue font-semibold hover:underline">Settings</a>.
				</p>

				<div class="flex flex-col gap-3">
					<div class="hidden lg:grid grid-cols-[1fr_1fr_1.2fr_1fr_auto] gap-2 text-[12px] font-semibold text-muted">
						<span>Type</span><span>Area</span><span>Area Type</span><span>Price</span><span>All Inclusive</span>
					</div>

					@foreach($configRows as $i => $row)
						<div class="grid grid-cols-1 lg:grid-cols-[1fr_1fr_1.2fr_1fr_auto] gap-2 items-center border-b border-line pb-3 lg:border-0 lg:pb-0">
							<div>
								<span class="lg:hidden field-label">Type</span>
								<select name="config_type[]" class="field">
									<option value="">— Select —</option>
									@foreach($unitTypes as $value => $label)
										<option value="{{ $value }}" @selected(($row['type'] ?? '') === $value)>{{ $label }}</option>
									@endforeach
									{{-- Keep a previously saved value that is no longer in the list. --}}
									@if(filled($row['type'] ?? '') && ! isset($unitTypes[$row['type']]))
										<option value="{{ $row['type'] }}" selected>{{ $row['type'] }}</option>
									@endif
								</select>
							</div>

							<div>
								<span class="lg:hidden field-label">Area</span>
								<input type="text" name="config_area[]" value="{{ $row['area'] ?? '' }}" placeholder="850 Sq.ft." class="field">
							</div>

							<div>
								<span class="lg:hidden field-label">Area Type</span>
								<select name="config_area_type[]" class="field">
									<option value="">— Select —</option>
									@foreach($areaTypes as $value => $label)
										<option value="{{ $value }}" @selected(($row['area_type'] ?? '') === $value)>{{ $label }}</option>
									@endforeach
									@if(filled($row['area_type'] ?? '') && ! isset($areaTypes[$row['area_type']]))
										<option value="{{ $row['area_type'] }}" selected>{{ $row['area_type'] }}</option>
									@endif
								</select>
							</div>

							<div>
								<span class="lg:hidden field-label">Price</span>
								<input type="text" name="config_price[]" value="{{ $row['price'] ?? '' }}" placeholder="₹ 1.25 Cr" class="field">
							</div>

							<label class="flex items-center gap-2 text-[13px] text-body cursor-pointer lg:justify-center lg:px-2 whitespace-nowrap">
								<input type="checkbox" name="config_all_inclusive[]" value="{{ $i }}"
								       class="w-4 h-4 accent-blue" @checked($row['all_inclusive'] ?? false)>
								<span class="lg:hidden">All inclusive price</span>
							</label>
						</div>
					@endforeach
				</div>
			</section>

			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Description &amp; Highlights</h2>
				<div class="grid gap-4">
					<x-admin.field name="description" label="Description" type="textarea" rows="6" :value="$project->description" />
					<x-admin.field name="highlights" label="Highlights" type="textarea" rows="4"
					               :value="is_array($project->highlights) ? implode(PHP_EOL, $project->highlights) : null"
					               help="One highlight per line." />
					<x-admin.field name="nearby_places" label="Nearby Places" type="textarea" rows="4"
					               :value="is_array($project->nearby_places) ? implode(PHP_EOL, $project->nearby_places) : null"
					               help="One place per line, e.g. Kharghar Railway Station — 2 km" />

					<div>
						<span class="field-label">Amenities</span>
						<div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-1">
							@foreach($amenityOptions as $amenity)
								<label class="flex items-center gap-2 text-[13px] text-body cursor-pointer">
									<input type="checkbox" name="amenities[]" value="{{ $amenity }}"
									       class="w-4 h-4 accent-blue"
									       @checked(in_array($amenity, old('amenities', $project->amenities ?? []), true))>
									{{ $amenity }}
								</label>
							@endforeach
						</div>

						<x-admin.field name="custom_amenities" label="Other Amenities" type="text" class="mt-3"
						               :value="old('custom_amenities', implode(', ', \App\Support\Amenities::customOnly($project->amenities ?? [])))"
						               placeholder="e.g. Solar Panels, Rain Water Harvesting"
						               help="Not in the list above? Type your own, separated by commas." />
					</div>
				</div>
			</section>

			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Media</h2>
				<div class="grid gap-4 sm:grid-cols-2">
					<div>
						<x-admin.field name="hero_image" label="Hero Image" type="file"
						               accept="image/jpeg,image/png,image/webp,image/avif" help="Max 5 MB." />
						@if($project->heroImageUrl())
							<img src="{{ $project->heroImageUrl() }}" alt="" class="mt-3 w-full max-w-[220px] rounded-[6px] border border-line">
						@endif
					</div>

					<div>
						<x-admin.field name="brochure" label="Brochure (PDF)" type="file" accept="application/pdf"
						               help="Max 10 MB. Visitors must submit their details before it downloads." />
						@if($project->hasBrochure())
							<div class="flex items-center gap-3 mt-2">
								<a href="{{ route('projects.brochure.download', $project) }}" target="_blank"
								   class="inline-flex items-center gap-1 text-[13px] text-blue font-semibold hover:underline">
									<x-icon name="download" class="w-4 h-4" /> Current brochure
								</a>
								<button type="button"
								        onclick="if(confirm('Remove this brochure?')) document.getElementById('del-brochure').submit();"
								        class="inline-flex items-center gap-1 text-[13px] text-red-600 font-semibold hover:underline cursor-pointer">
									<x-icon name="trash" class="w-4 h-4" /> Remove
								</button>
							</div>
						@endif
					</div>

					<x-admin.field name="gallery[]" label="Gallery Images" type="file" multiple
					               accept="image/jpeg,image/png,image/webp,image/avif" />
					<x-admin.field name="floor_plans[]" label="Floor Plans" type="file" multiple
					               accept="image/jpeg,image/png,image/webp,image/avif" />
				</div>

				@if($isEdit && $project->images->isNotEmpty())
					@foreach(['gallery' => 'Current Gallery', 'floor_plan' => 'Current Floor Plans'] as $type => $label)
						@php $items = $project->images->where('type', $type); @endphp
						@if($items->isNotEmpty())
							<div class="mt-5">
								<span class="field-label">{{ $label }}</span>
								<div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mt-2">
									@foreach($items as $image)
										<div class="relative">
											<img src="{{ $image->url() }}" alt="" class="w-full aspect-[4/3] object-cover rounded-[6px] border border-line">
											<button type="button"
											        onclick="if(confirm('Remove this image?')) document.getElementById('del-pimg-{{ $image->id }}').submit();"
											        class="absolute top-1 right-1 w-7 h-7 rounded-full bg-white/90 text-red-600 flex items-center justify-center shadow-sm hover:bg-white cursor-pointer">
												<x-icon name="trash" class="w-3.5 h-3.5" />
											</button>
										</div>
									@endforeach
								</div>
							</div>
						@endif
					@endforeach
				@endif
			</section>

			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Map, Contact &amp; SEO</h2>
				<div class="grid gap-4 sm:grid-cols-2">
					<x-admin.field name="map_url" label="Google Maps Embed URL" type="url" :value="$project->map_url" class="sm:col-span-2" />
					<x-admin.field name="contact_phone" label="Contact Phone" :value="$project->contact_phone" help="Blank uses site default." />
					<x-admin.field name="contact_whatsapp" label="Contact WhatsApp" :value="$project->contact_whatsapp" help="e.g. 918097985588" />
					<x-admin.field name="seo_title" label="SEO Title" :value="$project->seo_title" class="sm:col-span-2" />
					<x-admin.field name="seo_description" label="Meta Description" type="textarea" rows="3"
					               :value="$project->seo_description" class="sm:col-span-2" />
				</div>
			</section>
		</div>

		<aside class="card p-5 lg:sticky lg:top-5">
			<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Publish</h2>

			<div class="flex flex-col gap-1 mb-5">
				<x-admin.field name="is_published" label="Visibility" type="checkbox"
				               :value="$project->is_published" placeholder="Published (visible on site)" />
				<x-admin.field name="is_featured" label="Featured" type="checkbox"
				               :value="$project->is_featured" placeholder="Highlight this project" />
			</div>

			<button type="submit" class="btn btn-primary w-full">
				{{ $isEdit ? 'Update Project' : 'Create Project' }}
			</button>

			@if($isEdit)
				<a href="{{ route('projects.show', $project) }}" target="_blank" class="btn btn-outline w-full mt-2">
					<x-icon name="eye" class="w-[18px] h-[18px]" />
					View on Site
				</a>
			@endif
		</aside>
	</form>

	@if($isEdit)
		@foreach($project->images as $image)
			<form id="del-pimg-{{ $image->id }}" method="POST"
			      action="{{ route('admin.projects.images.destroy', $image) }}" class="hidden">
				@csrf
				@method('DELETE')
			</form>
		@endforeach

		@if($project->hasBrochure())
			<form id="del-brochure" method="POST"
			      action="{{ route('admin.projects.brochure.destroy', $project) }}" class="hidden">
				@csrf
				@method('DELETE')
			</form>
		@endif
	@endif
</x-layouts.admin>
