@php
    $socials = array_filter([
        'facebook' => setting('facebook'),
        'instagram' => setting('instagram'),
        'linkedin' => setting('linkedin'),
        'youtube' => setting('youtube'),
    ]);
@endphp

<footer class="bg-white border-t border-line mt-0">
	<div class="site-container py-10 lg:py-12">
		<div class="grid gap-8 lg:grid-cols-[1.2fr_1fr_1.3fr]">

			{{-- Brand --}}
			<div>
				<div class="flex items-center gap-2">
					<span class="text-navy"><x-icon name="home" class="w-8 h-8" /></span>
					<span class="flex flex-col leading-tight">
						<span class="font-display text-[21px] text-navy">{{ setting('site_name') }}</span>
						<span class="text-[11px] text-muted">{{ setting('tagline') }}</span>
					</span>
				</div>

				@if($socials)
					<div class="flex gap-3 mt-5">
						@foreach($socials as $network => $url)
							<a href="{{ $url }}" target="_blank" rel="noopener"
							   class="w-9 h-9 rounded-full bg-lightgray text-blue flex items-center justify-center hover:bg-lightblue transition-colors">
								<span class="sr-only">{{ ucfirst($network) }}</span>
								<x-icon :name="$network" class="w-[18px] h-[18px]" />
							</a>
						@endforeach
					</div>
				@endif
			</div>

			{{-- Quick links --}}
			<nav aria-label="Footer">
				<ul class="flex flex-col gap-2">
					@foreach([
						['Home', 'home'],
						['Properties', 'properties.index'],
						['Projects', 'projects.index'],
						['About Us', 'about'],
						['Contact', 'contact'],
					] as [$label, $route])
						<li>
							<a href="{{ route($route) }}" class="text-[14px] text-body hover:text-blue transition-colors">{{ $label }}</a>
						</li>
					@endforeach
				</ul>
			</nav>

			{{-- Contact --}}
			<ul class="flex flex-col gap-3">
				@if(setting('phone'))
					<li class="flex items-start gap-2 text-[14px] text-body">
						<span class="text-blue mt-0.5"><x-icon name="phone" class="w-[18px] h-[18px]" /></span>
						<a href="{{ tel_url() }}" class="hover:text-blue">{{ setting('phone') }}</a>
					</li>
				@endif
				@if(setting('email'))
					<li class="flex items-start gap-2 text-[14px] text-body">
						<span class="text-blue mt-0.5"><x-icon name="mail" class="w-[18px] h-[18px]" /></span>
						<a href="mailto:{{ setting('email') }}" class="hover:text-blue">{{ setting('email') }}</a>
					</li>
				@endif
				@if(setting('address'))
					<li class="flex items-start gap-2 text-[14px] text-body">
						<span class="text-blue mt-0.5"><x-icon name="map-pin" class="w-[18px] h-[18px]" /></span>
						<span>{{ setting('address') }}</span>
					</li>
				@endif
			</ul>
		</div>
	</div>

	<div class="border-t border-line">
		<div class="site-container py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-[12px] text-muted">
			<p>&copy; {{ date('Y') }} {{ setting('site_name') }}. All Rights Reserved.</p>
			<p>Building Better Tomorrows.</p>
		</div>
	</div>
</footer>
