<x-layouts.admin title="Settings" subtitle="Contact details, social links and site-wide content.">

	<form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-[820px] flex flex-col gap-5">
		@csrf
		@method('PUT')

		@foreach($schema as $group => $fields)
			<section class="card p-5">
				<h2 class="font-sans text-[15px] font-bold text-ink mb-4">{{ $group }}</h2>

				<div class="grid gap-4 sm:grid-cols-2">
					@foreach($fields as $key => $config)
						<x-admin.field
							:name="$key"
							:label="$config['label']"
							:type="$config['type'] ?? 'text'"
							:value="setting($key)"
							:help="$config['help'] ?? null"
							:rows="($config['type'] ?? null) === 'textarea' ? 8 : 4"
							:required="str_contains($config['rules'], 'required')"
							:class="($config['type'] ?? null) === 'textarea' ? 'sm:col-span-2' : ''" />
					@endforeach
				</div>
			</section>
		@endforeach

		<div class="flex gap-3">
			<button type="submit" class="btn btn-primary">Save Settings</button>
			<a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Cancel</a>
		</div>
	</form>
</x-layouts.admin>
