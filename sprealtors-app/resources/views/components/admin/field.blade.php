@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'options' => null,
    'placeholder' => null,
    'required' => false,
    'help' => null,
    'rows' => 4,
])

@php
    $id = 'field-'.str_replace(['[', ']', '.'], ['-', '', '-'], $name);
    $current = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
	<label for="{{ $id }}" class="field-label">
		{{ $label }}
		@if($required)<span class="text-red-600">*</span>@endif
	</label>

	@if($type === 'textarea')
		<textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
		          placeholder="{{ $placeholder }}"
		          @required($required)
		          class="field h-auto py-3 {{ $hasError ? 'border-red-400' : '' }}">{{ $current }}</textarea>

	@elseif($type === 'select')
		<select id="{{ $id }}" name="{{ $name }}" @required($required)
		        class="field {{ $hasError ? 'border-red-400' : '' }}">
			@if($placeholder !== null)
				<option value="">{{ $placeholder }}</option>
			@endif
			@foreach($options ?? [] as $optValue => $optLabel)
				<option value="{{ $optValue }}" @selected((string) $current === (string) $optValue)>{{ $optLabel }}</option>
			@endforeach
		</select>

	@elseif($type === 'checkbox')
		<label class="flex items-center gap-2 text-[14px] text-body cursor-pointer h-[46px]">
			<input type="hidden" name="{{ $name }}" value="0">
			<input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1"
			       class="w-4 h-4 accent-blue" @checked((bool) $current)>
			{{ $placeholder ?? 'Yes' }}
		</label>

	@elseif($type === 'file')
		<input type="file" id="{{ $id }}" name="{{ $name }}"
		       {{ $attributes->only('accept', 'multiple') }}
		       class="block w-full text-[13px] text-body file:mr-3 file:py-2 file:px-4 file:rounded-[6px] file:border-0 file:bg-lightblue file:text-blue file:font-semibold file:cursor-pointer cursor-pointer">

	@else
		<input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $current }}"
		       placeholder="{{ $placeholder }}" @required($required)
		       {{ $attributes->only(['step', 'min', 'max']) }}
		       class="field {{ $hasError ? 'border-red-400' : '' }}">
	@endif

	@if($help)
		<p class="text-[12px] text-muted mt-1 mb-0">{{ $help }}</p>
	@endif

	@error($name)
		<p class="text-[12px] text-red-600 mt-1 mb-0">{{ $message }}</p>
	@enderror
</div>
