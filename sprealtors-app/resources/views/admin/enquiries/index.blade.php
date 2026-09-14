<x-layouts.admin title="Enquiries" subtitle="Leads received from the website.">

	{{-- Status tabs --}}
	<div class="flex flex-wrap gap-2 mb-5">
		<a href="{{ route('admin.enquiries.index') }}"
		   @class([
		       'px-4 py-2 rounded-[6px] text-[13px] font-semibold border transition-colors',
		       'bg-blue text-white border-blue' => ! request('status'),
		       'bg-white text-body border-line hover:border-blue' => request('status'),
		   ])>
			All ({{ array_sum($statusCounts) }})
		</a>
		@foreach(\App\Models\Enquiry::statusOptions() as $value => $label)
			<a href="{{ route('admin.enquiries.index', ['status' => $value]) }}"
			   @class([
			       'px-4 py-2 rounded-[6px] text-[13px] font-semibold border transition-colors',
			       'bg-blue text-white border-blue' => request('status') === $value,
			       'bg-white text-body border-line hover:border-blue' => request('status') !== $value,
			   ])>
				{{ $label }} ({{ $statusCounts[$value] ?? 0 }})
			</a>
		@endforeach
	</div>

	<form method="GET" class="flex flex-wrap gap-3 mb-5">
		@if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
		<input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, phone or email…"
		       class="field w-full sm:w-[300px]">
		<select name="source" class="field w-full sm:w-[180px]">
			<option value="">All sources</option>
			@foreach(\App\Models\Enquiry::sourceOptions() as $value => $label)
				<option value="{{ $value }}" @selected(request('source') === $value)>{{ $label }}</option>
			@endforeach
		</select>
		<button type="submit" class="btn btn-primary">Filter</button>
		@if(request()->hasAny(['q', 'source']))
			<a href="{{ route('admin.enquiries.index', ['status' => request('status')]) }}" class="btn btn-outline">Clear</a>
		@endif
	</form>

	<div class="card overflow-hidden">
		@if($enquiries->isEmpty())
			<p class="px-5 py-10 text-center text-[14px] text-muted m-0">No enquiries found.</p>
		@else
			<div class="overflow-x-auto">
				<table class="w-full text-[13px]">
					<thead class="bg-offwhite text-muted">
						<tr>
							<th class="text-left font-semibold px-5 py-3">Name</th>
							<th class="text-left font-semibold px-5 py-3">Phone</th>
							<th class="text-left font-semibold px-5 py-3 hidden lg:table-cell">Email</th>
							<th class="text-left font-semibold px-5 py-3 hidden md:table-cell">Regarding</th>
							<th class="text-left font-semibold px-5 py-3">Status</th>
							<th class="text-left font-semibold px-5 py-3 hidden sm:table-cell">Received</th>
							<th class="text-right font-semibold px-5 py-3">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($enquiries as $enquiry)
							<tr class="border-t border-line hover:bg-offwhite">
								<td class="px-5 py-3">
									<a href="{{ route('admin.enquiries.show', $enquiry) }}" class="font-semibold text-ink hover:text-blue">
										{{ $enquiry->name }}
									</a>
								</td>
								<td class="px-5 py-3">
									<a href="tel:{{ $enquiry->phone }}" class="text-body hover:text-blue">{{ $enquiry->phone }}</a>
								</td>
								<td class="px-5 py-3 text-body hidden lg:table-cell">{{ $enquiry->email ?: '—' }}</td>
								<td class="px-5 py-3 text-muted hidden md:table-cell">{{ $enquiry->subject() ?? $enquiry->sourceLabel() }}</td>
								<td class="px-5 py-3"><x-admin.status-badge :status="$enquiry->status" /></td>
								<td class="px-5 py-3 text-muted hidden sm:table-cell">{{ $enquiry->created_at->format('d M Y') }}</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-end gap-1">
										<a href="{{ route('admin.enquiries.show', $enquiry) }}"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue" title="View">
											<x-icon name="eye" class="w-4 h-4" />
										</a>
										<form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}"
										      onsubmit="return confirm('Delete this enquiry?');">
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

	<div class="mt-5">{{ $enquiries->links('pagination.default') }}</div>
</x-layouts.admin>
