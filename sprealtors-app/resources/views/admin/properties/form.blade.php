@php
    $isEdit = $property->exists;
@endphp

<x-layouts.admin
	:title="$isEdit ? 'Edit Property' : 'Add Property'"
	:subtitle="$isEdit ? $property->title : 'Create a new property listing.'">

	<x-slot:actions>
		<a href="{{ route('admin.properties.index') }}" class="btn btn-outline">Back to list</a>
	</x-slot:actions>

	<form method="POST"
	      action="{{ $isEdit ? route('admin.properties.update', $property) : route('admin.properties.store') }}"
	      enctype="multipart/form-data"
	      class="grid gap-5 lg:grid-cols-[1fr_320px] items-start">
		@csrf
		@if($isEdit) @method('PUT') @endif

		{{-- MAIN COLUMN --}}
		<div class="flex flex-col gap-5 min-w-0">

			{{-- Basic --}}
			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Basic Details</h2>
				<div class="grid gap-4 sm:grid-cols-2">
					<x-admin.field name="title" label="Property Title" :value="$property->title" required class="sm:col-span-2" />
					<x-admin.field name="slug" label="Slug" :value="$property->slug"
					               placeholder="auto-generated from title"
					               help="URL: /property/your-slug" class="sm:col-span-2" />

					<x-admin.field name="property_type" label="Property Type" type="select" required
					               :value="$property->property_type"
					               :options="['residential' => 'Residential', 'commercial' => 'Commercial']" />

					<x-admin.field name="purpose" label="Purpose" type="select" required
					               :value="$property->purpose"
					               :options="['buy' => 'Buy (For Sale)', 'rent' => 'Rent (For Rent)']" />

					<x-admin.field name="configuration" label="Configuration" type="select"
					               :value="$property->configuration" placeholder="— Select —"
					               :options="\App\Models\Property::configurationOptions()" />

					<x-admin.field name="status" label="Status" type="select" required
					               :value="$property->status"
					               :options="\App\Models\Property::statusOptions()" />

					<x-admin.field name="location_id" label="Location" type="select"
					               :value="$property->location_id" placeholder="— Select —"
					               :options="$locations->pluck('name', 'id')" />

					<x-admin.field name="furnishing" label="Furnishing" type="select"
					               :value="$property->furnishing" placeholder="— Select —"
					               :options="\App\Models\Property::furnishingOptions()" />

					<x-admin.field name="address" label="Address" :value="$property->address" class="sm:col-span-2" />
				</div>
			</section>

			{{-- Pricing & specs --}}
			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Pricing &amp; Specifications</h2>
				<div class="grid gap-4 sm:grid-cols-3">
					<x-admin.field name="price" label="Price (₹)" type="number" step="1" min="0"
					               :value="$property->price ? (int) $property->price : null"
					               help="Full amount in rupees, e.g. 8500000" />
					<x-admin.field name="price_negotiable" label="Negotiable" type="checkbox"
					               :value="$property->price_negotiable" placeholder="Price is negotiable" />
					<x-admin.field name="is_monthly" label="Monthly Rent" type="checkbox"
					               :value="$property->is_monthly" placeholder="Show as / month" />

					<x-admin.field name="area" label="Area" type="number" min="0" :value="$property->area" />
					<x-admin.field name="area_unit" label="Area Unit" :value="$property->area_unit ?: 'Sq.ft.'" />
					<x-admin.field name="car_parking" label="Car Parking" type="number" min="0" :value="$property->car_parking" />

					<x-admin.field name="bedrooms" label="Bedrooms" type="number" min="0" :value="$property->bedrooms" />
					<x-admin.field name="bathrooms" label="Bathrooms" type="number" min="0" :value="$property->bathrooms" />
					<x-admin.field name="possession" label="Possession" :value="$property->possession" placeholder="e.g. Dec 2026" />
				</div>
			</section>

			{{-- Content --}}
			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Description &amp; Highlights</h2>
				<div class="grid gap-4">
					<x-admin.field name="description" label="Description" type="textarea" rows="6" :value="$property->description" />
					<x-admin.field name="highlights" label="Highlights" type="textarea" rows="5"
					               :value="is_array($property->highlights) ? implode(PHP_EOL, $property->highlights) : null"
					               help="One highlight per line." />

					<div>
						<span class="field-label">Amenities</span>
						<div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-1">
							@foreach($amenityOptions as $amenity)
								<label class="flex items-center gap-2 text-[13px] text-body cursor-pointer">
									<input type="checkbox" name="amenities[]" value="{{ $amenity }}"
									       class="w-4 h-4 accent-blue"
									       @checked(in_array($amenity, old('amenities', $property->amenities ?? []), true))>
									{{ $amenity }}
								</label>
							@endforeach
						</div>

						<x-admin.field name="custom_amenities" label="Other Amenities" type="text" class="mt-3"
						               :value="old('custom_amenities', implode(', ', \App\Support\Amenities::customOnly($property->amenities ?? [])))"
						               placeholder="e.g. Solar Panels, Rain Water Harvesting"
						               help="Not in the list above? Type your own, separated by commas." />
					</div>
				</div>
			</section>

			{{-- Media --}}
			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Images</h2>

				<div class="grid gap-4 sm:grid-cols-2">
					<div>
						<x-admin.field name="main_image" label="Main Image" type="file"
						               accept="image/jpeg,image/png,image/webp,image/avif"
						               help="JPG, PNG, WebP or AVIF. Max 5 MB." />
						@if($property->mainImageUrl())
							<img src="{{ $property->mainImageUrl() }}" alt="" class="mt-3 w-full max-w-[220px] rounded-[6px] border border-line">
						@endif
					</div>

					<x-admin.field name="gallery[]" label="Gallery Images" type="file" multiple
					               accept="image/jpeg,image/png,image/webp,image/avif"
					               help="Select multiple files to add to the gallery." />
				</div>

				@if($isEdit && $property->images->isNotEmpty())
					<div class="mt-5">
						<span class="field-label">Current Gallery</span>
						<div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mt-2">
							@foreach($property->images as $image)
								<div class="relative group">
									<img src="{{ $image->url() }}" alt="" class="w-full aspect-[4/3] object-cover rounded-[6px] border border-line">
									<button type="button"
									        onclick="if(confirm('Remove this image?')) document.getElementById('del-img-{{ $image->id }}').submit();"
									        class="absolute top-1 right-1 w-7 h-7 rounded-full bg-white/90 text-red-600 flex items-center justify-center shadow-sm hover:bg-white cursor-pointer"
									        title="Remove image">
										<x-icon name="trash" class="w-3.5 h-3.5" />
									</button>
								</div>
							@endforeach
						</div>
					</div>
				@endif
			</section>

			{{-- Extra --}}
			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Legal, Map &amp; SEO</h2>
				<div class="grid gap-4 sm:grid-cols-2">
					<x-admin.field name="rera_number" label="RERA Number" :value="$property->rera_number" />
					<x-admin.field name="map_url" label="Google Maps Embed URL" type="url" :value="$property->map_url" />
					<x-admin.field name="contact_phone" label="Contact Phone" :value="$property->contact_phone"
					               help="Leave blank to use the site default." />
					<x-admin.field name="contact_whatsapp" label="Contact WhatsApp" :value="$property->contact_whatsapp"
					               help="Digits with country code, e.g. 919324473328" />
					<x-admin.field name="seo_title" label="SEO Title" :value="$property->seo_title" class="sm:col-span-2" />
					<x-admin.field name="seo_description" label="Meta Description" type="textarea" rows="3"
					               :value="$property->seo_description" class="sm:col-span-2" />
				</div>
			</section>
		</div>

		{{-- SIDEBAR --}}
		<aside class="card p-5 lg:sticky lg:top-5">
			<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Publish</h2>

			<div class="flex flex-col gap-1 mb-5">
				<x-admin.field name="is_published" label="Visibility" type="checkbox"
				               :value="$property->is_published" placeholder="Published (visible on site)" />
				<x-admin.field name="is_featured" label="Featured" type="checkbox"
				               :value="$property->is_featured" placeholder="Show on home page" />
			</div>

			<button type="submit" class="btn btn-primary w-full">
				{{ $isEdit ? 'Update Property' : 'Create Property' }}
			</button>

			@if($isEdit)
				<a href="{{ route('properties.show', $property) }}" target="_blank" class="btn btn-outline w-full mt-2">
					<x-icon name="eye" class="w-[18px] h-[18px]" />
					View on Site
				</a>
			@endif
		</aside>
	</form>

	{{-- Image delete forms (kept outside the main form to avoid nesting) --}}
	@if($isEdit)
		@foreach($property->images as $image)
			<form id="del-img-{{ $image->id }}" method="POST"
			      action="{{ route('admin.properties.images.destroy', $image) }}" class="hidden">
				@csrf
				@method('DELETE')
			</form>
		@endforeach
	@endif
</x-layouts.admin>
