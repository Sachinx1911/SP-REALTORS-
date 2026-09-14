<x-layouts.admin title="Locations" subtitle="Areas you serve — used by property filters and the home page.">
	<x-slot:actions>
		<a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
			<x-icon name="plus" class="w-[18px] h-[18px]" />
			Add Location
		</a>
	</x-slot:actions>

	<div class="card overflow-hidden">
		@if($locations->isEmpty())
			<p class="px-5 py-10 text-center text-[14px] text-muted m-0">No locations yet.</p>
		@else
			<div class="overflow-x-auto">
				<table class="w-full text-[13px]">
					<thead class="bg-offwhite text-muted">
						<tr>
							<th class="text-left font-semibold px-5 py-3">Location</th>
							<th class="text-left font-semibold px-5 py-3 hidden sm:table-cell">Slug</th>
							<th class="text-left font-semibold px-5 py-3">Properties</th>
							<th class="text-left font-semibold px-5 py-3 hidden sm:table-cell">Projects</th>
							<th class="text-left font-semibold px-5 py-3">Status</th>
							<th class="text-right font-semibold px-5 py-3">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($locations as $location)
							<tr class="border-t border-line hover:bg-offwhite">
								<td class="px-5 py-3">
									<div class="flex items-center gap-3">
										<span class="w-10 h-10 rounded-[4px] bg-lightblue overflow-hidden shrink-0 flex items-center justify-center text-blue/40">
											@if($location->imageUrl())
												<img src="{{ $location->imageUrl() }}" alt="" class="w-full h-full object-cover">
											@else
												<x-icon name="map-pin" class="w-4 h-4" />
											@endif
										</span>
										<a href="{{ route('admin.locations.edit', $location) }}" class="font-semibold text-ink hover:text-blue">
											{{ $location->name }}
										</a>
									</div>
								</td>
								<td class="px-5 py-3 text-muted hidden sm:table-cell">{{ $location->slug }}</td>
								<td class="px-5 py-3 text-body">{{ $location->properties_count }}</td>
								<td class="px-5 py-3 text-body hidden sm:table-cell">{{ $location->projects_count }}</td>
								<td class="px-5 py-3">
									@if($location->is_published)
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-green/10 text-green-dark text-[11px] font-bold">Published</span>
									@else
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-lightgray text-muted text-[11px] font-bold">Hidden</span>
									@endif
								</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-end gap-1">
										<a href="{{ route('admin.locations.edit', $location) }}"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue" title="Edit">
											<x-icon name="pencil" class="w-4 h-4" />
										</a>
										<form method="POST" action="{{ route('admin.locations.destroy', $location) }}"
										      onsubmit="return confirm('Delete this location? Properties will keep their data but lose this location.');">
											@csrf
											@method('DELETE')
											<button type="submit"
											        class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-red-600 hover:border-red-300 cursor-pointer" title="Delete">
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

	<div class="mt-5">{{ $locations->links('pagination.default') }}</div>
</x-layouts.admin>
