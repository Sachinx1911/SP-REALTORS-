@php $isEdit = $testimonial->exists; @endphp

<x-layouts.admin
	:title="$isEdit ? 'Edit Testimonial' : 'Add Testimonial'"
	:subtitle="$isEdit ? $testimonial->client_name : 'Add a new client review.'">

	<x-slot:actions>
		<a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline">Back to list</a>
	</x-slot:actions>

	<form method="POST"
	      action="{{ $isEdit ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}"
	      enctype="multipart/form-data"
	      class="max-w-[720px] flex flex-col gap-5">
		@csrf
		@if($isEdit) @method('PUT') @endif

		<section class="card p-5">
			<div class="grid gap-4 sm:grid-cols-2">
				<x-admin.field name="client_name" label="Client Name" :value="$testimonial->client_name" required />
				<x-admin.field name="role" label="Role / Detail" :value="$testimonial->role"
				               placeholder="e.g. 2 BHK Buyer" />

				<x-admin.field name="review" label="Review" type="textarea" rows="4"
				               :value="$testimonial->review" required class="sm:col-span-2" />

				<x-admin.field name="property_type" label="Property Type" :value="$testimonial->property_type"
				               placeholder="e.g. Residential" />

				<x-admin.field name="rating" label="Rating" type="select" :value="$testimonial->rating"
				               :options="[5 => '5 Stars', 4 => '4 Stars', 3 => '3 Stars', 2 => '2 Stars', 1 => '1 Star']" />

				<div>
					<x-admin.field name="photo" label="Client Photo" type="file"
					               accept="image/jpeg,image/png,image/webp,image/avif"
					               help="Optional. Initials are shown if empty." />
					@if($testimonial->photoUrl())
						<img src="{{ $testimonial->photoUrl() }}" alt="" class="mt-3 w-16 h-16 rounded-full object-cover border border-line">
					@endif
				</div>

				<div class="flex flex-col gap-1">
					<x-admin.field name="sort_order" label="Sort Order" type="number" min="0" :value="$testimonial->sort_order" />
					<x-admin.field name="is_published" label="Visibility" type="checkbox"
					               :value="$testimonial->is_published" placeholder="Published" />
				</div>
			</div>
		</section>

		<div class="flex gap-3">
			<button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Testimonial' : 'Create Testimonial' }}</button>
			<a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline">Cancel</a>
		</div>
	</form>
</x-layouts.admin>
