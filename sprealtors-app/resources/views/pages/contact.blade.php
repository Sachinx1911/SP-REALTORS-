<x-layouts.site
	title="Contact SP REALTORS — Navi Mumbai Real Estate"
	description="Get in touch with SP REALTORS for any property related queries in Navi Mumbai. Call, WhatsApp or send us a message and we'll get back to you shortly.">

	<x-page-hero
		eyebrow="Get in Touch"
		heading="Contact Us"
		text="We'd love to hear from you. Get in touch for any property related queries or assistance."
		badge="Let's Build a Better Tomorrow"
		:image="asset('images/hero-building.webp')" />

	{{-- Info cards --}}
	<section class="site-container -mt-6 relative z-10">
		<ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
			@foreach(array_filter([
				setting('phone') ? ['phone', 'Call Us', setting('phone'), 'Mon - Sat, 9 AM - 7 PM', tel_url()] : null,
				setting('email') ? ['mail', 'Email Us', setting('email'), 'We reply within 24 hours', 'mailto:'.setting('email')] : null,
				setting('address') ? ['map-pin', 'Visit Our Office', setting('address'), 'By appointment', null] : null,
				setting('working_hours') ? ['clock', 'Working Hours', setting('working_hours'), '(Sunday by appointment)', null] : null,
			]) as [$icon, $title, $value, $sub, $href])
				<li class="card p-5 text-center">
					<span class="w-11 h-11 rounded-full bg-lightblue text-blue inline-flex items-center justify-center mb-3">
						<x-icon :name="$icon" class="w-5 h-5" />
					</span>
					<strong class="block text-[14px] text-ink mb-1">{{ $title }}</strong>
					@if($href)
						<a href="{{ $href }}" class="block text-[13px] text-ink hover:text-blue break-words">{{ $value }}</a>
					@else
						<span class="block text-[13px] text-ink break-words">{{ $value }}</span>
					@endif
					<small class="block text-[11px] text-muted mt-1">{{ $sub }}</small>
				</li>
			@endforeach
		</ul>
	</section>

	{{-- Form + map --}}
	<section class="section">
		<div class="site-container">
			<div class="grid gap-8 lg:grid-cols-2">

				{{-- Form --}}
				<div class="card p-6">
					<h2 class="text-[22px] mb-1">Send Us a Message</h2>
					<p class="text-[13px] text-muted mb-5">Fill in the form below and we'll get back to you shortly.</p>

					<x-enquiry-form source="contact" button-label="Send Message" />

					<p class="flex items-center gap-2 text-[12px] text-muted mt-4 mb-0">
						<x-icon name="lock" class="w-4 h-4 shrink-0" />
						Your information is safe with us. We respect your privacy.
					</p>

					<div class="flex flex-col sm:flex-row gap-3 mt-5 pt-5 border-t border-line">
						<a href="{{ whatsapp_url('Hello SP REALTORS, I have a property related query.') }}"
						   target="_blank" rel="noopener" class="btn btn-whatsapp flex-1">
							<x-icon name="whatsapp" class="w-[18px] h-[18px]" />
							Chat on WhatsApp
						</a>
						<a href="{{ tel_url() }}" class="btn btn-outline flex-1">
							<x-icon name="phone" class="w-[18px] h-[18px]" />
							Call Now
						</a>
					</div>
				</div>

				{{-- Map --}}
				<div>
					<h2 class="text-[22px] mb-1">Our Office Location</h2>
					<p class="text-[13px] text-muted mb-5">Visit us at our office or find us on the map below.</p>

					@if(setting('map_url'))
						<div class="rounded-[8px] overflow-hidden aspect-[16/10] bg-lightgray mb-4">
							<iframe src="{{ setting('map_url') }}" title="SP REALTORS office location"
							        class="w-full h-full border-0" loading="lazy"
							        referrerpolicy="no-referrer-when-downgrade"></iframe>
						</div>
					@else
						<div class="rounded-[8px] aspect-[16/10] bg-lightgray flex items-center justify-center text-muted text-[13px] mb-4">
							<span class="text-center px-6">
								<span class="block text-blue/40 mb-2"><x-icon name="map-pin" class="w-8 h-8 mx-auto" /></span>
								Map will appear here once configured in the admin panel.
							</span>
						</div>
					@endif

					<div class="card p-4 flex items-start gap-3">
						<span class="text-blue shrink-0 mt-0.5"><x-icon name="map-pin" class="w-5 h-5" /></span>
						<div class="grow">
							<strong class="block text-[14px] text-ink">{{ setting('site_name') }}</strong>
							<p class="text-[13px] text-body m-0">{{ setting('address') }}</p>
						</div>
						@if(setting('map_url'))
							<a href="{{ setting('map_url') }}" target="_blank" rel="noopener"
							   class="btn btn-outline shrink-0 text-[12px] px-3">
								Get Directions
							</a>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- FAQ --}}
	<section class="section pt-0">
		<div class="site-container">
			<div class="mb-6">
				<h2 class="text-[24px] lg:text-[30px]">Frequently Asked Questions</h2>
				<p class="text-[13px] text-muted m-0">Quick answers to common questions.</p>
			</div>

			<div class="grid gap-3 md:grid-cols-2">
				@foreach($faqs as $i => $faq)
					<div class="border border-line rounded-[8px] overflow-hidden" data-faq-item>
						<button type="button" data-faq-question aria-expanded="false" aria-controls="faq-{{ $i }}"
						        class="w-full flex items-center justify-between gap-3 p-4 bg-white text-left text-[14px] font-semibold text-ink cursor-pointer hover:bg-offwhite transition-colors">
							{{ $faq['question'] }}
							<span data-faq-toggle class="text-blue text-[20px] leading-none transition-transform shrink-0">+</span>
						</button>
						<div id="faq-{{ $i }}" data-faq-answer class="px-4 max-h-0 overflow-hidden transition-[max-height] duration-200">
							<p class="text-[13px] text-muted m-0">{{ $faq['answer'] }}</p>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>

	<x-cta-banner />
</x-layouts.site>
