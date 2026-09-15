@php $isEdit = $user->exists; @endphp

<x-layouts.admin
	:title="$isEdit ? 'Edit Team Member' : 'Add Team Member'"
	:subtitle="$isEdit ? $user->email : 'Give someone else a login for this admin panel.'">

	<x-slot:actions>
		<a href="{{ route('admin.users.index') }}" class="btn btn-outline">Back to list</a>
	</x-slot:actions>

	<form method="POST"
	      action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}"
	      class="max-w-[560px] flex flex-col gap-5">
		@csrf
		@if($isEdit) @method('PUT') @endif

		<section class="card p-5">
			<div class="grid gap-4">
				<x-admin.field name="name" label="Full Name" :value="$user->name" required />
				<x-admin.field name="email" label="Email" type="email" :value="$user->email" required
				               help="Used to log in at /admin/login." />

				<x-admin.field name="password" label="{{ $isEdit ? 'New Password' : 'Password' }}" type="password"
				               :required="! $isEdit"
				               help="{{ $isEdit ? 'Leave blank to keep the current password.' : 'At least 8 characters.' }}" />
				<x-admin.field name="password_confirmation" label="Confirm Password" type="password"
				               :required="! $isEdit" />

				<x-admin.field name="is_admin" label="Admin Access" type="checkbox"
				               :value="$user->is_admin" placeholder="Can log into this admin panel"
				               help="Team members without this ticked cannot sign in at all — there is no other account type yet." />
			</div>
		</section>

		<div class="flex gap-3">
			<button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Team Member' : 'Create Team Member' }}</button>
			<a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
		</div>
	</form>
</x-layouts.admin>
