@php
    $nav = [
        ['label' => 'Home', 'route' => 'home', 'active' => request()->routeIs('home')],
        ['label' => 'Properties', 'route' => 'properties.index', 'active' => request()->routeIs('properties.*')],
        ['label' => 'Projects', 'route' => 'projects.index', 'active' => request()->routeIs('projects.*')],
        ['label' => 'About Us', 'route' => 'about', 'active' => request()->routeIs('about')],
        ['label' => 'Contact', 'route' => 'contact', 'active' => request()->routeIs('contact')],
    ];
@endphp

<header class="sticky top-0 z-50 bg-white border-b border-line">
	<div class="site-container h-16 lg:h-[72px] flex items-center justify-between gap-4">

		{{-- Brand --}}
		<a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
			<img src="{{ asset('images/logo-mark.png') }}" alt="" class="w-10 h-10 lg:w-11 lg:h-11 object-contain">
			<span class="flex flex-col leading-tight">
				<span class="font-display text-[19px] lg:text-[21px] text-navy">{{ setting('site_name') }}</span>
				<span class="text-[10px] lg:text-[11px] text-muted">{{ setting('tagline') }}</span>
			</span>
		</a>

		{{-- Desktop nav --}}
		<nav class="hidden lg:block" aria-label="Primary">
			<ul class="flex items-center gap-7">
				@foreach($nav as $item)
					<li>
						<a href="{{ route($item['route']) }}"
						   @class([
						       'text-[14px] font-semibold py-6 border-b-2 transition-colors',
						       'text-blue border-blue' => $item['active'],
						       'text-ink border-transparent hover:text-blue' => ! $item['active'],
						   ])
						   @if($item['active']) aria-current="page" @endif>
							{{ $item['label'] }}
						</a>
					</li>
				@endforeach
			</ul>
		</nav>

		{{-- Actions --}}
		<div class="flex items-center gap-3 shrink-0">
			<a href="{{ tel_url() }}" class="hidden lg:flex items-center gap-2">
				<span class="text-blue"><x-icon name="phone" class="w-5 h-5" /></span>
				<span class="flex flex-col leading-tight">
					<span class="text-[13px] font-bold text-navy">{{ setting('phone') }}</span>
					<span class="text-[11px] text-muted">Call for Assistance</span>
				</span>
			</a>

			<a href="{{ whatsapp_url('Hello SP REALTORS, I would like to know more about your properties.') }}"
			   target="_blank" rel="noopener"
			   class="btn btn-whatsapp hidden sm:inline-flex">
				<x-icon name="whatsapp" class="w-[18px] h-[18px]" />
				WhatsApp Us
			</a>

			<button type="button"
			        class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-[6px] border border-line text-navy"
			        data-nav-toggle
			        aria-expanded="false"
			        aria-controls="mobile-drawer">
				<span class="sr-only">Menu</span>
				<span data-nav-icon-open><x-icon name="menu" class="w-6 h-6" /></span>
				<span data-nav-icon-close class="hidden"><x-icon name="close" class="w-6 h-6" /></span>
			</button>
		</div>
	</div>

	{{-- Mobile drawer --}}
	<div id="mobile-drawer"
	     class="lg:hidden fixed inset-x-0 top-16 bottom-0 bg-white z-40 translate-x-full transition-transform duration-200 overflow-y-auto"
	     data-nav-drawer>
		<nav class="px-5 py-6" aria-label="Mobile">
			<ul class="flex flex-col gap-1">
				@foreach($nav as $item)
					<li>
						<a href="{{ route($item['route']) }}"
						   @class([
						       'block px-4 py-3 rounded-[6px] font-semibold',
						       'bg-lightblue text-blue' => $item['active'],
						       'text-ink hover:bg-lightgray' => ! $item['active'],
						   ])>
							{{ $item['label'] }}
						</a>
					</li>
				@endforeach
			</ul>

			<div class="mt-6 grid grid-cols-2 gap-3">
				<a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" class="btn btn-whatsapp">
					<x-icon name="whatsapp" class="w-[18px] h-[18px]" />
					WhatsApp
				</a>
				<a href="{{ tel_url() }}" class="btn btn-outline">
					<x-icon name="phone" class="w-[18px] h-[18px]" />
					Call
				</a>
			</div>
		</nav>
	</div>
</header>
