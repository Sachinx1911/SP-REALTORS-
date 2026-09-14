<x-layouts.admin title="Projects" subtitle="Manage residential and commercial projects.">
	<x-slot:actions>
		<a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
			<x-icon name="plus" class="w-[18px] h-[18px]" />
			Add Project
		</a>
	</x-slot:actions>

	<form method="GET" class="flex flex-wrap gap-3 mb-5">
		<input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name…"
		       class="field w-full sm:w-[280px]">
		<button type="submit" class="btn btn-primary">Search</button>
		@if(request('q'))
			<a href="{{ route('admin.projects.index') }}" class="btn btn-outline">Clear</a>
		@endif
	</form>

	<div class="card overflow-hidden">
		@if($projects->isEmpty())
			<p class="px-5 py-10 text-center text-[14px] text-muted m-0">
				No projects yet. <a href="{{ route('admin.projects.create') }}" class="text-blue font-semibold hover:underline">Add your first project</a>.
			</p>
		@else
			<div class="overflow-x-auto">
				<table class="w-full text-[13px]">
					<thead class="bg-offwhite text-muted">
						<tr>
							<th class="text-left font-semibold px-5 py-3">Project</th>
							<th class="text-left font-semibold px-5 py-3 hidden md:table-cell">Developer</th>
							<th class="text-left font-semibold px-5 py-3 hidden md:table-cell">Location</th>
							<th class="text-left font-semibold px-5 py-3">Starting Price</th>
							<th class="text-left font-semibold px-5 py-3">Status</th>
							<th class="text-right font-semibold px-5 py-3">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($projects as $project)
							<tr class="border-t border-line hover:bg-offwhite">
								<td class="px-5 py-3">
									<div class="flex items-center gap-3">
										<span class="w-12 h-9 rounded-[4px] bg-lightblue overflow-hidden shrink-0 flex items-center justify-center text-blue/40">
											@if($project->heroImageUrl())
												<img src="{{ $project->heroImageUrl() }}" alt="" class="w-full h-full object-cover">
											@else
												<x-icon name="building" class="w-4 h-4" />
											@endif
										</span>
										<a href="{{ route('admin.projects.edit', $project) }}" class="font-semibold text-ink hover:text-blue">
											{{ $project->name }}
										</a>
									</div>
								</td>
								<td class="px-5 py-3 text-body hidden md:table-cell">{{ $project->developer ?? '—' }}</td>
								<td class="px-5 py-3 text-body hidden md:table-cell">{{ $project->location?->name ?? '—' }}</td>
								<td class="px-5 py-3 font-semibold text-blue">{{ $project->formattedStartingPrice() }}</td>
								<td class="px-5 py-3">
									@if($project->is_published)
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-green/10 text-green-dark text-[11px] font-bold">Published</span>
									@else
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-lightgray text-muted text-[11px] font-bold">Draft</span>
									@endif
								</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-end gap-1">
										<a href="{{ route('projects.show', $project) }}" target="_blank"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue" title="View">
											<x-icon name="eye" class="w-4 h-4" />
										</a>
										<a href="{{ route('admin.projects.edit', $project) }}"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue" title="Edit">
											<x-icon name="pencil" class="w-4 h-4" />
										</a>
										<form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
										      onsubmit="return confirm('Delete this project? This cannot be undone.');">
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

	<div class="mt-5">{{ $projects->links('pagination.default') }}</div>
</x-layouts.admin>
