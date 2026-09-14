@props(['testimonial'])

<figure class="card p-5 flex flex-col gap-3 h-full">
	<span class="text-gold"><x-icon name="quote" class="w-7 h-7" /></span>

	<blockquote class="text-[13px] lg:text-[14px] text-body grow">
		{{ $testimonial->review }}
	</blockquote>

	<figcaption class="flex items-center gap-3 border-t border-line pt-3">
		@if($testimonial->photoUrl())
			<img src="{{ $testimonial->photoUrl() }}" alt="" loading="lazy" class="w-11 h-11 rounded-full object-cover">
		@else
			<span class="w-11 h-11 rounded-full bg-lightblue text-blue flex items-center justify-center font-bold text-[13px]">
				{{ $testimonial->initials() }}
			</span>
		@endif
		<span>
			<strong class="block text-[13px] text-ink">{{ $testimonial->client_name }}</strong>
			@if($testimonial->role)
				<small class="block text-[11px] text-muted">{{ $testimonial->role }}</small>
			@endif
		</span>
	</figcaption>
</figure>
