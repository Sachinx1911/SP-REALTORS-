<x-layouts.admin title="Enquiry Details" :subtitle="$enquiry->name.' — '.$enquiry->created_at->format('d M Y, g:i A')">
	<x-slot:actions>
		<a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline">Back to list</a>
	</x-slot:actions>

	<div class="grid gap-5 lg:grid-cols-[1fr_320px] items-start max-w-[1000px]">

		{{-- Details --}}
		<section class="card p-5">
			<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Contact Details</h2>

			<dl class="grid sm:grid-cols-2 gap-4 text-[14px]">
				<div>
					<dt class="text-[12px] text-muted mb-0.5">Name</dt>
					<dd class="font-semibold text-ink m-0">{{ $enquiry->name }}</dd>
				</div>
				<div>
					<dt class="text-[12px] text-muted mb-0.5">Phone</dt>
					<dd class="m-0">
						<a href="tel:{{ $enquiry->phone }}" class="font-semibold text-blue hover:underline">{{ $enquiry->phone }}</a>
					</dd>
				</div>
				<div>
					<dt class="text-[12px] text-muted mb-0.5">Email</dt>
					<dd class="m-0">
						@if($enquiry->email)
							<a href="mailto:{{ $enquiry->email }}" class="font-semibold text-blue hover:underline">{{ $enquiry->email }}</a>
						@else
							<span class="text-muted">—</span>
						@endif
					</dd>
				</div>
				<div>
					<dt class="text-[12px] text-muted mb-0.5">Source</dt>
					<dd class="font-semibold text-ink m-0">{{ $enquiry->sourceLabel() }}</dd>
				</div>

				@if($enquiry->property)
					<div class="sm:col-span-2">
						<dt class="text-[12px] text-muted mb-0.5">Property</dt>
						<dd class="m-0">
							<a href="{{ route('admin.properties.edit', $enquiry->property) }}" class="font-semibold text-blue hover:underline">
								{{ $enquiry->property->title }}
							</a>
						</dd>
					</div>
				@endif

				@if($enquiry->project)
					<div class="sm:col-span-2">
						<dt class="text-[12px] text-muted mb-0.5">Project</dt>
						<dd class="m-0">
							<a href="{{ route('admin.projects.edit', $enquiry->project) }}" class="font-semibold text-blue hover:underline">
								{{ $enquiry->project->name }}
							</a>
						</dd>
					</div>
				@endif

				<div class="sm:col-span-2">
					<dt class="text-[12px] text-muted mb-0.5">Message</dt>
					<dd class="text-body m-0 whitespace-pre-line">{{ $enquiry->message ?: '—' }}</dd>
				</div>
			</dl>

			<div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-line">
				<a href="tel:{{ $enquiry->phone }}" class="btn btn-primary">
					<x-icon name="phone" class="w-[18px] h-[18px]" />
					Call
				</a>
				<a href="https://wa.me/{{ preg_replace('/\D/', '', $enquiry->phone) }}" target="_blank" rel="noopener" class="btn btn-whatsapp">
					<x-icon name="whatsapp" class="w-[18px] h-[18px]" />
					WhatsApp
				</a>
				@if($enquiry->email)
					<a href="mailto:{{ $enquiry->email }}" class="btn btn-outline">
						<x-icon name="mail" class="w-[18px] h-[18px]" />
						Email
					</a>
				@endif
			</div>
		</section>

		{{-- Status --}}
		<aside class="card p-5">
			<h2 class="font-sans text-[15px] font-bold text-ink mb-4">Lead Status</h2>

			<form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="flex flex-col gap-4">
				@csrf
				@method('PATCH')

				<x-admin.field name="status" label="Status" type="select"
				               :value="$enquiry->status" :options="\App\Models\Enquiry::statusOptions()" required />

				<x-admin.field name="admin_notes" label="Internal Notes" type="textarea" rows="5"
				               :value="$enquiry->admin_notes"
				               help="Only visible to admins." />

				<button type="submit" class="btn btn-primary w-full">Save Changes</button>
			</form>

			<p class="text-[12px] text-muted mt-4 mb-0">
				Received {{ $enquiry->created_at->diffForHumans() }}
				@if($enquiry->ip_address)
					<br>IP: {{ $enquiry->ip_address }}
				@endif
			</p>
		</aside>
	</div>
</x-layouts.admin>
