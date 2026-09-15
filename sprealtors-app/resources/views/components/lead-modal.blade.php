@props([
    'id',
    'action',
    'source' => 'popup',
    'project' => null,
    'title',
    'text' => null,
    'buttonLabel' => 'Submit',
    'hidden' => true,
])

{{-- Shared lead-capture dialog. Opened by JS via data-modal-open="{id}". --}}
<div id="{{ $id }}"
     data-modal
     {{ $attributes->merge([
         'class' => ($hidden ? 'hidden ' : '').'fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4',
     ]) }}
     role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">

	<div class="absolute inset-0 bg-navy/60" data-modal-close></div>

	<div class="relative w-full sm:max-w-[440px] bg-white rounded-t-[16px] sm:rounded-[10px] shadow-[0_16px_40px_rgba(9,43,80,0.25)] max-h-[92vh] overflow-y-auto">

		<button type="button" data-modal-close
		        class="absolute top-3 right-3 w-9 h-9 rounded-full border border-line bg-white text-muted hover:text-navy hover:border-navy flex items-center justify-center cursor-pointer"
		        aria-label="Close">
			<x-icon name="close" class="w-4 h-4" />
		</button>

		<div class="p-6">
			<h2 id="{{ $id }}-title" class="text-[21px] mb-1 pr-8">{{ $title }}</h2>
			@if($text)
				<p class="text-[13px] text-muted mb-4">{{ $text }}</p>
			@endif

			<form method="POST" action="{{ $action }}" class="flex flex-col gap-3">
				@csrf
				<input type="hidden" name="source" value="{{ $source }}">
				@if($project)
					<input type="hidden" name="project_id" value="{{ $project->id }}">
				@endif

				{{-- Honeypot --}}
				<div class="hidden" aria-hidden="true">
					<label for="website-{{ $id }}">Website</label>
					<input type="text" id="website-{{ $id }}" name="website" tabindex="-1" autocomplete="off">
				</div>

				<div>
					<label for="name-{{ $id }}" class="sr-only">Name</label>
					<input type="text" id="name-{{ $id }}" name="name" class="field" placeholder="Name *" required maxlength="120">
				</div>

				<div>
					<label for="phone-{{ $id }}" class="sr-only">Phone Number</label>
					<input type="tel" id="phone-{{ $id }}" name="phone" class="field" placeholder="Phone Number *" required maxlength="20">
				</div>

				<div>
					<label for="email-{{ $id }}" class="sr-only">Email Address</label>
					<input type="email" id="email-{{ $id }}" name="email" class="field" placeholder="Email Address" maxlength="180">
				</div>

				{{ $slot ?? '' }}

				<button type="submit" class="btn btn-whatsapp w-full mt-1">
					{{ $buttonLabel }}
				</button>

				<p class="flex items-center gap-2 text-[11px] text-muted m-0">
					<x-icon name="lock" class="w-3.5 h-3.5 shrink-0" />
					Your details are safe with us. We never share them.
				</p>
			</form>
		</div>
	</div>
</div>
