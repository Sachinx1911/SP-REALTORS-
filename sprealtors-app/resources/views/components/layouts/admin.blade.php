@props(['title' => 'Admin'])

@php
    $nav = [
        ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard')],
        ['label' => 'Properties', 'icon' => 'home', 'route' => 'admin.properties.index', 'active' => request()->routeIs('admin.properties.*')],
        ['label' => 'Projects', 'icon' => 'building', 'route' => 'admin.projects.index', 'active' => request()->routeIs('admin.projects.*')],
        ['label' => 'Locations', 'icon' => 'map-pin', 'route' => 'admin.locations.index', 'active' => request()->routeIs('admin.locations.*')],
        ['label' => 'Testimonials', 'icon' => 'quote', 'route' => 'admin.testimonials.index', 'active' => request()->routeIs('admin.testimonials.*')],
        ['label' => 'Enquiries', 'icon' => 'inbox', 'route' => 'admin.enquiries.index', 'active' => request()->routeIs('admin.enquiries.*')],
        ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings.edit', 'active' => request()->routeIs('admin.settings.*')],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="robots" content="noindex, nofollow">
	<title>{{ $title }} — SP REALTORS Admin</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">

	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-lightgray antialiased">

<div class="min-h-screen lg:flex">

	{{-- Sidebar --}}
	<aside class="lg:w-[250px] lg:shrink-0 bg-navy text-white lg:min-h-screen">
		<div class="flex items-center justify-between lg:block p-5">
			<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
				<x-icon name="home" class="w-7 h-7" />
				<span class="flex flex-col leading-tight">
					<span class="font-display text-[18px]">SP REALTORS</span>
					<span class="text-[10px] text-white/60">Admin Panel</span>
				</span>
			</a>

			<button type="button" data-nav-toggle aria-expanded="false" aria-controls="admin-nav"
			        class="lg:hidden w-10 h-10 rounded-[6px] border border-white/20 flex items-center justify-center">
				<span class="sr-only">Menu</span>
				<span data-nav-icon-open><x-icon name="menu" class="w-5 h-5" /></span>
				<span data-nav-icon-close class="hidden"><x-icon name="close" class="w-5 h-5" /></span>
			</button>
		</div>

		<nav id="admin-nav" data-nav-drawer="collapse"
		     class="hidden lg:block px-3 pb-5" aria-label="Admin">
			<ul class="flex flex-col gap-1">
				@foreach($nav as $item)
					<li>
						<a href="{{ route($item['route']) }}"
						   @class([
						       'flex items-center gap-3 px-3 py-2.5 rounded-[6px] text-[14px] font-medium transition-colors',
						       'bg-white/15 text-white' => $item['active'],
						       'text-white/75 hover:bg-white/10 hover:text-white' => ! $item['active'],
						   ])>
							<x-icon :name="$item['icon']" class="w-[18px] h-[18px] shrink-0" />
							{{ $item['label'] }}
						</a>
					</li>
				@endforeach
			</ul>

			<div class="mt-6 pt-4 border-t border-white/15 flex flex-col gap-1">
				<a href="{{ route('home') }}" target="_blank"
				   class="flex items-center gap-3 px-3 py-2.5 rounded-[6px] text-[14px] text-white/75 hover:bg-white/10 hover:text-white">
					<x-icon name="eye" class="w-[18px] h-[18px]" />
					View Site
				</a>
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<button type="submit"
					        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-[6px] text-[14px] text-white/75 hover:bg-white/10 hover:text-white cursor-pointer">
						<x-icon name="logout" class="w-[18px] h-[18px]" />
						Log Out
					</button>
				</form>
			</div>
		</nav>
	</aside>

	{{-- Main --}}
	<div class="grow min-w-0">
		<header class="bg-white border-b border-line">
			<div class="px-5 lg:px-8 py-4 flex items-center justify-between gap-4">
				<div>
					<h1 class="font-sans text-[20px] font-bold text-ink m-0">{{ $title }}</h1>
					@isset($subtitle)
						<p class="text-[13px] text-muted m-0">{{ $subtitle }}</p>
					@endisset
				</div>
				@isset($actions)
					<div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
				@endisset
			</div>
		</header>

		<main class="p-5 lg:p-8">
			@if(session('status'))
				<div class="mb-5 rounded-[6px] bg-green/10 border border-green/30 px-4 py-3 text-[14px] text-green-dark" role="status">
					{{ session('status') }}
				</div>
			@endif

			@if($errors->any())
				<div class="mb-5 rounded-[6px] bg-red-50 border border-red-200 px-4 py-3 text-[14px] text-red-700" role="alert">
					<strong class="block mb-1">Please fix the following:</strong>
					<ul class="list-disc list-inside space-y-0.5">
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			{{ $slot }}
		</main>
	</div>
</div>

</body>
</html>
