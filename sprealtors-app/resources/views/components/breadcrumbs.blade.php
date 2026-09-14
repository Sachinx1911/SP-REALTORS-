@props(['items' => []])

@php
    // $items: [['label' => 'Properties', 'url' => '...'], ['label' => 'Current page']]
    $trail = array_merge([['label' => 'Home', 'url' => route('home')]], $items);

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($trail)->values()->map(fn ($item, $i) => array_filter([
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['label'],
            'item' => $item['url'] ?? null,
        ]))->all(),
    ];
@endphp

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'py-4 text-[12px] text-muted']) }}>
	<ol class="flex flex-wrap items-center gap-1.5">
		@foreach($trail as $i => $item)
			<li class="flex items-center gap-1.5">
				@if(! $loop->last && isset($item['url']))
					<a href="{{ $item['url'] }}" class="hover:text-blue transition-colors">{{ $item['label'] }}</a>
					<x-icon name="chevron-right" class="w-3 h-3 text-line" />
				@else
					<span class="text-ink font-semibold" aria-current="page">{{ $item['label'] }}</span>
				@endif
			</li>
		@endforeach
	</ol>
</nav>

<script type="application/ld+json">
	{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
