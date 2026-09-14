<x-layouts.admin title="Properties" subtitle="Manage all property listings.">
	<x-slot:actions>
		<a href="{{ route('admin.properties.create') }}" class="btn btn-primary">
			<x-icon name="plus" class="w-[18px] h-[18px]" />
			Add Property
		</a>
	</x-slot:actions>

	{{-- Search / filter --}}
	<form method="GET" class="flex flex-wrap gap-3 mb-5">
		<input type="search" name="q" value="{{ request('q') }}" placeholder="Search by title…"
		       class="field w-full sm:w-[280px]">
		<select name="status" class="field w-full sm:w-[180px]">
			<option value="">All statuses</option>
			<option value="published" @selected(request('status') === 'published')>Published</option>
			<option value="draft" @selected(request('status') === 'draft')>Draft</option>
		</select>
		<button type="submit" class="btn btn-primary">Filter</button>
		@if(request()->hasAny(['q', 'status']))
			<a href="{{ route('admin.properties.index') }}" class="btn btn-outline">Clear</a>
		@endif
	</form>

	<div class="card overflow-hidden">
		@if($properties->isEmpty())
			<p class="px-5 py-10 text-center text-[14px] text-muted m-0">
				No properties yet. <a href="{{ route('admin.properties.create') }}" class="text-blue font-semibold hover:underline">Add your first property</a>.
			</p>
		@else
			<div class="overflow-x-auto">
				<table class="w-full text-[13px]">
					<thead class="bg-offwhite text-muted">
						<tr>
							<th class="text-left font-semibold px-5 py-3">Property</th>
							<th class="text-left font-semibold px-5 py-3 hidden md:table-cell">Location</th>
							<th class="text-left font-semibold px-5 py-3">Price</th>
							<th class="text-left font-semibold px-5 py-3 hidden sm:table-cell">Purpose</th>
							<th class="text-left font-semibold px-5 py-3">Status</th>
							<th class="text-right font-semibold px-5 py-3">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($properties as $property)
							<tr class="border-t border-line hover:bg-offwhite">
								<td class="px-5 py-3">
									<div class="flex items-center gap-3">
										<span class="w-12 h-9 rounded-[4px] bg-lightblue overflow-hidden shrink-0 flex items-center justify-center text-blue/40">
											@if($property->mainImageUrl())
												<img src="{{ $property->mainImageUrl() }}" alt="" class="w-full h-full object-cover">
											@else
												<x-icon name="home" class="w-4 h-4" />
											@endif
										</span>
										<span>
											<a href="{{ route('admin.properties.edit', $property) }}" class="font-semibold text-ink hover:text-blue">
												{{ $property->title }}
											</a>
											@if($property->is_featured)
												<span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-[3px] bg-gold/15 text-gold-dark text-[10px] font-bold">Featured</span>
											@endif
										</span>
									</div>
								</td>
								<td class="px-5 py-3 text-body hidden md:table-cell">{{ $property->location?->name ?? '—' }}</td>
								<td class="px-5 py-3 font-semibold text-blue">{{ $property->formattedPrice() }}</td>
								<td class="px-5 py-3 text-body hidden sm:table-cell">{{ $property->badgeLabel() }}</td>
								<td class="px-5 py-3">
									@if($property->is_published)
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-green/10 text-green-dark text-[11px] font-bold">Published</span>
									@else
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-lightgray text-muted text-[11px] font-bold">Draft</span>
									@endif
								</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-end gap-1">
										<a href="{{ route('properties.show', $property) }}" target="_blank"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue"
										   title="View on site">
											<x-icon name="eye" class="w-4 h-4" />
										</a>
										<a href="{{ route('admin.properties.edit', $property) }}"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue"
										   title="Edit">
											<x-icon name="pencil" class="w-4 h-4" />
										</a>
										<form method="POST" action="{{ route('admin.properties.destroy', $property) }}"
										      onsubmit="return confirm('Delete this property? This cannot be undone.');">
											@csrf
											@method('DELETE')
											<button type="submit"
											        class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-red-600 hover:border-red-300 cursor-pointer"
											        title="Delete">
												<x-icon name="trash" class="w-4 h-4" />
											</button>
										</form>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>

	<div class="mt-5">{{ $properties->links('pagination.default') }}</div>
</x-layouts.admin>
