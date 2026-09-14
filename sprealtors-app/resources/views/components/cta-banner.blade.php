@props([
    'heading' => 'Looking for the Right Property?',
    'text' => "Tell us your requirement and we'll help you find the best options.",
])

<section class="bg-navy text-white">
	<div class="site-container py-8 lg:py-9">
		<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
			<div>
				<h2 class="text-white text-[24px] lg:text-[28px] mb-1">{{ $heading }}</h2>
				<p class="text-white/80 text-[14px] m-0">{{ $text }}</p>
			</div>

			<div class="flex flex-col sm:flex-row gap-3 shrink-0">
				<a href="{{ whatsapp_url('Hello SP REALTORS, I am looking for a property. Please help.') }}"
				   target="_blank" rel="noopener" class="btn btn-whatsapp">
					<x-icon name="whatsapp" class="w-[18px] h-[18px]" />
					Chat on WhatsApp
				</a>
				<a href="{{ tel_url() }}" class="btn btn-outline-light">
					<x-icon name="phone" class="w-[18px] h-[18px]" />
					Call Now
				</a>
			</div>
		</div>
	</div>
</section>
