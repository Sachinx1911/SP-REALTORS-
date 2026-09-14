@props([
    'eyebrow' => null,
    'heading',
    'text' => null,
    'badge' => null,
    'image' => null,
    'compact' => false,
])

<section class="relative bg-lightblue overflow-hidden">
	@if($image)
		<div class="absolute inset-0 hidden lg:block">
			<img src="{{ $image }}" alt="" class="w-full h-full object-cover">
			<div class="absolute inset-0 bg-[linear-gradient(100deg,rgba(245,249,253,.94)_0%,rgba(245,249,253,.65)_38%,rgba(245,249,253,.1)_62%,rgba(245,249,253,0)_74%)]"></div>
		</div>
	@endif

	<div class="site-container relative {{ $compact ? 'py-8 lg:py-10' : 'py-10 lg:py-14' }}">
		<div class="lg:max-w-[560px]">
			@if($eyebrow)
				<span class="eyebrow">{{ $eyebrow }}</span>
			@endif

			<h1 class="{{ $eyebrow ? 'mt-3' : '' }} text-[30px] sm:text-[36px] lg:text-[44px] leading-[1.1]">
				{{ $heading }}
			</h1>

			@if($text)
				<p class="mt-3 text-[14px] lg:text-[15px] text-body max-w-[52ch]">{{ $text }}</p>
			@endif

			{{ $slot }}
		</div>

		@if($badge)
			<span class="hidden lg:block absolute top-8 right-20 font-display italic text-navy text-[18px] leading-snug text-right max-w-[220px]">
				{{ $badge }}
				<span class="block w-4/5 ml-auto h-[3px] bg-gold rounded-full mt-1 -rotate-3"></span>
			</span>
		@endif
	</div>
</section>
