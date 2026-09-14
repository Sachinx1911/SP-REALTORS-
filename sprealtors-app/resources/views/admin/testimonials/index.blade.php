<x-layouts.admin title="Testimonials" subtitle="Client reviews shown on the home page.">
	<x-slot:actions>
		<a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
			<x-icon name="plus" class="w-[18px] h-[18px]" />
			Add Testimonial
		</a>
	</x-slot:actions>

	<div class="card overflow-hidden">
		@if($testimonials->isEmpty())
			<p class="px-5 py-10 text-center text-[14px] text-muted m-0">No testimonials yet.</p>
		@else
			<div class="overflow-x-auto">
				<table class="w-full text-[13px]">
					<thead class="bg-offwhite text-muted">
						<tr>
							<th class="text-left font-semibold px-5 py-3">Client</th>
							<th class="text-left font-semibold px-5 py-3 hidden md:table-cell">Review</th>
							<th class="text-left font-semibold px-5 py-3 hidden sm:table-cell">Rating</th>
							<th class="text-left font-semibold px-5 py-3">Status</th>
							<th class="text-right font-semibold px-5 py-3">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($testimonials as $testimonial)
							<tr class="border-t border-line hover:bg-offwhite">
								<td class="px-5 py-3">
									<div class="flex items-center gap-3">
										<span class="w-9 h-9 rounded-full bg-lightblue text-blue overflow-hidden shrink-0 flex items-center justify-center font-bold text-[11px]">
											@if($testimonial->photoUrl())
												<img src="{{ $testimonial->photoUrl() }}" alt="" class="w-full h-full object-cover">
											@else
												{{ $testimonial->initials() }}
											@endif
										</span>
										<span>
											<a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="font-semibold text-ink hover:text-blue block">
												{{ $testimonial->client_name }}
											</a>
											@if($testimonial->role)
												<small class="text-muted text-[11px]">{{ $testimonial->role }}</small>
											@endif
										</span>
									</div>
								</td>
								<td class="px-5 py-3 text-body hidden md:table-cell max-w-[380px]">
									{{ Str::limit($testimonial->review, 90) }}
								</td>
								<td class="px-5 py-3 text-gold hidden sm:table-cell">{{ str_repeat('★', $testimonial->rating) }}</td>
								<td class="px-5 py-3">
									@if($testimonial->is_published)
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-green/10 text-green-dark text-[11px] font-bold">Published</span>
									@else
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-lightgray text-muted text-[11px] font-bold">Hidden</span>
									@endif
								</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-end gap-1">
										<a href="{{ route('admin.testimonials.edit', $testimonial) }}"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue" title="Edit">
											<x-icon name="pencil" class="w-4 h-4" />
										</a>
										<form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}"
										      onsubmit="return confirm('Delete this testimonial?');">
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

	<div class="mt-5">{{ $testimonials->links('pagination.default') }}</div>
</x-layouts.admin>
