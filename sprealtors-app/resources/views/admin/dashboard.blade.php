<x-layouts.admin title="Dashboard" subtitle="Overview of your website content and leads.">

	{{-- Stat cards --}}
	<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		@foreach($stats as $stat)
			<a href="{{ $stat['route'] }}" class="card card-hover p-5 block">
				<span class="w-10 h-10 rounded-full bg-lightblue text-blue inline-flex items-center justify-center mb-3">
					<x-icon :name="$stat['icon']" class="w-5 h-5" />
				</span>
				<strong class="block font-display text-[28px] text-navy leading-none">{{ $stat['value'] }}</strong>
				<span class="block text-[13px] text-muted mt-1">{{ $stat['label'] }}</span>
			</a>
		@endforeach
	</div>

	{{-- Enquiries summary --}}
	<div class="grid gap-4 lg:grid-cols-[280px_1fr]">
		<a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="card card-hover p-5 block">
			<span class="w-10 h-10 rounded-full bg-green/10 text-green inline-flex items-center justify-center mb-3">
				<x-icon name="inbox" class="w-5 h-5" />
			</span>
			<strong class="block font-display text-[28px] text-navy leading-none">{{ $newEnquiries }}</strong>
			<span class="block text-[13px] text-muted mt-1">New enquiries</span>
			<span class="block text-[12px] text-muted mt-2">{{ $totalEnquiries }} total received</span>
		</a>

		<div class="card overflow-hidden">
			<div class="flex items-center justify-between px-5 py-4 border-b border-line">
				<h2 class="font-sans text-[15px] font-bold text-ink m-0">Recent Enquiries</h2>
				<a href="{{ route('admin.enquiries.index') }}" class="text-[13px] font-semibold text-blue hover:underline">View all</a>
			</div>

			@if($recentEnquiries->isEmpty())
				<p class="px-5 py-8 text-center text-[13px] text-muted m-0">No enquiries yet.</p>
			@else
				<div class="overflow-x-auto">
					<table class="w-full text-[13px]">
						<thead class="bg-offwhite text-muted">
							<tr>
								<th class="text-left font-semibold px-5 py-2.5">Name</th>
								<th class="text-left font-semibold px-5 py-2.5">Phone</th>
								<th class="text-left font-semibold px-5 py-2.5 hidden sm:table-cell">Regarding</th>
								<th class="text-left font-semibold px-5 py-2.5">Status</th>
								<th class="text-left font-semibold px-5 py-2.5 hidden md:table-cell">Received</th>
							</tr>
						</thead>
						<tbody>
							@foreach($recentEnquiries as $enquiry)
								<tr class="border-t border-line hover:bg-offwhite">
									<td class="px-5 py-3">
										<a href="{{ route('admin.enquiries.show', $enquiry) }}" class="font-semibold text-ink hover:text-blue">
											{{ $enquiry->name }}
										</a>
									</td>
									<td class="px-5 py-3 text-body">{{ $enquiry->phone }}</td>
									<td class="px-5 py-3 text-muted hidden sm:table-cell">{{ $enquiry->subject() ?? $enquiry->sourceLabel() }}</td>
									<td class="px-5 py-3">
										<x-admin.status-badge :status="$enquiry->status" />
									</td>
									<td class="px-5 py-3 text-muted hidden md:table-cell">{{ $enquiry->created_at->diffForHumans() }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		</div>
	</div>
</x-layouts.admin>
