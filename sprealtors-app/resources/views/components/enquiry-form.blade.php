@props([
    'source' => 'contact',
    'property' => null,
    'project' => null,
    'title' => null,
    'buttonLabel' => 'Send Message',
])

<div id="enquiry" {{ $attributes }}>
	@if(session('enquiry_success'))
		<div class="mb-4 rounded-[6px] bg-green/10 border border-green/30 px-4 py-3 text-[14px] text-green-dark" role="status">
			{{ session('enquiry_success') }}
		</div>
	@endif

	@if($errors->any())
		<div class="mb-4 rounded-[6px] bg-red-50 border border-red-200 px-4 py-3 text-[14px] text-red-700" role="alert">
			<ul class="list-disc list-inside space-y-0.5">
				@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if($title)
		<h3 class="text-[19px] lg:text-[21px] mb-4">{{ $title }}</h3>
	@endif

	<form action="{{ route('enquiry.store') }}" method="POST" class="flex flex-col gap-3">
		@csrf
		<input type="hidden" name="source" value="{{ $source }}">
		@if($property)
			<input type="hidden" name="property_id" value="{{ $property->id }}">
		@endif
		@if($project)
			<input type="hidden" name="project_id" value="{{ $project->id }}">
		@endif

		{{-- Honeypot --}}
		<div class="hidden" aria-hidden="true">
			<label for="website-{{ $source }}">Website</label>
			<input type="text" id="website-{{ $source }}" name="website" tabindex="-1" autocomplete="off">
		</div>

		<div>
			<label for="name-{{ $source }}" class="sr-only">Name</label>
			<input type="text" id="name-{{ $source }}" name="name" class="field" placeholder="Name *"
			       value="{{ old('name') }}" required maxlength="120">
		</div>

		<div>
			<label for="phone-{{ $source }}" class="sr-only">Phone Number</label>
			<input type="tel" id="phone-{{ $source }}" name="phone" class="field" placeholder="Phone Number *"
			       value="{{ old('phone') }}" required maxlength="20">
		</div>

		<div>
			<label for="email-{{ $source }}" class="sr-only">Email Address</label>
			<input type="email" id="email-{{ $source }}" name="email" class="field" placeholder="Email Address"
			       value="{{ old('email') }}" maxlength="180">
		</div>

		<div>
			<label for="message-{{ $source }}" class="sr-only">Message</label>
			<textarea id="message-{{ $source }}" name="message" class="field"
			          placeholder="{{ $property || $project ? 'Your Requirement / Message *' : 'Your Message *' }}"
			          maxlength="2000">{{ old('message') }}</textarea>
		</div>

		<button type="submit" class="btn btn-whatsapp w-full">
			<x-icon name="send" class="w-[18px] h-[18px]" />
			{{ $buttonLabel }}
		</button>
	</form>
</div>
