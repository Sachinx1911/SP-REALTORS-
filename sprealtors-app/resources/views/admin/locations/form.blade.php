@php $isEdit = $location->exists; @endphp

<x-layouts.admin
	:title="$isEdit ? 'Edit Location' : 'Add Location'"
	:subtitle="$isEdit ? $location->name : 'Create a new service area.'">

	<x-slot:actions>
		<a href="{{ route('admin.locations.index') }}" class="btn btn-outline">Back to list</a>
	</x-slot:actions>

	<form method="POST"
	      action="{{ $isEdit ? route('admin.locations.update', $location) : route('admin.locations.store') }}"
	      enctype="multipart/form-data"
	      class="max-w-[720px] flex flex-col gap-5">
		@csrf
		@if($isEdit) @method('PUT') @endif

		<section class="card p-5">
			<div class="grid gap-4 sm:grid-cols-2">
				<x-admin.field name="name" label="Location Name" :value="$location->name" required />
				<x-admin.field name="slug" label="Slug" :value="$location->slug" placeholder="auto-generated" />

				<x-admin.field name="description" label="Description" type="textarea" rows="3"
				               :value="$location->description" class="sm:col-span-2" />

				<div>
					<x-admin.field name="image" label="Image" type="file"
					               accept="image/jpeg,image/png,image/webp,image/avif"
					               help="Shown in the 'Areas We Serve' section. Max 5 MB." />
					@if($location->imageUrl())
						<img src="{{ $location->imageUrl() }}" alt="" class="mt-3 w-full max-w-[200px] rounded-[6px] border border-line">
					@endif
				</div>

				<div class="flex flex-col gap-1">
					<x-admin.field name="sort_order" label="Sort Order" type="number" min="0" :value="$location->sort_order" />
					<x-admin.field name="is_published" label="Visibility" type="checkbox"
					               :value="$location->is_published" placeholder="Published" />
				</div>

				<x-admin.field name="seo_title" label="SEO Title" :value="$location->seo_title" class="sm:col-span-2" />
				<x-admin.field name="seo_description" label="Meta Description" type="textarea" rows="2"
				               :value="$location->seo_description" class="sm:col-span-2" />
			</div>
		</section>

		<div class="flex gap-3">
			<button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Location' : 'Create Location' }}</button>
			<a href="{{ route('admin.locations.index') }}" class="btn btn-outline">Cancel</a>
		</div>
	</form>
</x-layouts.admin>
