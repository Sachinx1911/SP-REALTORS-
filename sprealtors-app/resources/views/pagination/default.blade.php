@if ($paginator->hasPages())
	<nav class="flex justify-center" role="navigation" aria-label="Pagination">
		<ul class="flex flex-wrap items-center gap-2">
			{{-- Previous --}}
			@if ($paginator->onFirstPage())
				<li>
					<span class="inline-flex items-center justify-center min-w-[38px] h-[38px] px-3 rounded-[4px] border border-line text-[13px] text-muted opacity-50">
						<x-icon name="chevron-left" class="w-4 h-4" />
					</span>
				</li>
			@else
				<li>
					<a href="{{ $paginator->previousPageUrl() }}" rel="prev"
					   class="inline-flex items-center justify-center min-w-[38px] h-[38px] px-3 rounded-[4px] border border-line text-[13px] font-semibold text-ink hover:border-blue hover:text-blue transition-colors">
						<span class="sr-only">Previous</span>
						<x-icon name="chevron-left" class="w-4 h-4" />
					</a>
				</li>
			@endif

			{{-- Page numbers --}}
			@foreach ($elements as $element)
				@if (is_string($element))
					<li>
						<span class="inline-flex items-center justify-center min-w-[38px] h-[38px] px-2 text-[13px] text-muted">{{ $element }}</span>
					</li>
				@endif

				@if (is_array($element))
					@foreach ($element as $page => $url)
						<li>
							@if ($page == $paginator->currentPage())
								<span aria-current="page"
								      class="inline-flex items-center justify-center min-w-[38px] h-[38px] px-3 rounded-[4px] border border-blue bg-blue text-[13px] font-semibold text-white">
									{{ $page }}
								</span>
							@else
								<a href="{{ $url }}"
								   class="inline-flex items-center justify-center min-w-[38px] h-[38px] px-3 rounded-[4px] border border-line text-[13px] font-semibold text-ink hover:border-blue hover:text-blue transition-colors">
									{{ $page }}
								</a>
							@endif
						</li>
					@endforeach
				@endif
			@endforeach

			{{-- Next --}}
			@if ($paginator->hasMorePages())
				<li>
					<a href="{{ $paginator->nextPageUrl() }}" rel="next"
					   class="inline-flex items-center gap-1 justify-center min-w-[38px] h-[38px] px-3 rounded-[4px] border border-line text-[13px] font-semibold text-ink hover:border-blue hover:text-blue transition-colors">
						Next
						<x-icon name="chevron-right" class="w-4 h-4" />
					</a>
				</li>
			@else
				<li>
					<span class="inline-flex items-center gap-1 justify-center min-w-[38px] h-[38px] px-3 rounded-[4px] border border-line text-[13px] text-muted opacity-50">
						Next
						<x-icon name="chevron-right" class="w-4 h-4" />
					</span>
				</li>
			@endif
		</ul>
	</nav>
@endif
