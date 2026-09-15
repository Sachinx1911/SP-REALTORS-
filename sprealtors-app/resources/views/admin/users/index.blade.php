<x-layouts.admin title="Team" subtitle="People who can log into this admin panel.">
	<x-slot:actions>
		<a href="{{ route('admin.users.create') }}" class="btn btn-primary">
			<x-icon name="plus" class="w-[18px] h-[18px]" />
			Add Team Member
		</a>
	</x-slot:actions>

	<div class="card overflow-hidden">
		@if($users->isEmpty())
			<p class="px-5 py-10 text-center text-[14px] text-muted m-0">No team members yet.</p>
		@else
			<div class="overflow-x-auto">
				<table class="w-full text-[13px]">
					<thead class="bg-offwhite text-muted">
						<tr>
							<th class="text-left font-semibold px-5 py-3">Name</th>
							<th class="text-left font-semibold px-5 py-3 hidden sm:table-cell">Email</th>
							<th class="text-left font-semibold px-5 py-3">Role</th>
							<th class="text-right font-semibold px-5 py-3">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($users as $user)
							<tr class="border-t border-line hover:bg-offwhite">
								<td class="px-5 py-3">
									<a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-ink hover:text-blue">
										{{ $user->name }}
									</a>
									@if($user->id === auth()->id())
										<span class="ml-1 text-[11px] text-muted">(you)</span>
									@endif
									<span class="sm:hidden block text-muted text-[12px]">{{ $user->email }}</span>
								</td>
								<td class="px-5 py-3 text-body hidden sm:table-cell">{{ $user->email }}</td>
								<td class="px-5 py-3">
									@if($user->isAdmin())
										<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[4px] bg-lightblue text-blue text-[11px] font-bold">
											<x-icon name="shield" class="w-3 h-3" /> Admin
										</span>
									@elseif($user->isManager())
										<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[4px] bg-gold/15 text-gold text-[11px] font-bold">
											<x-icon name="users" class="w-3 h-3" /> Manager
										</span>
									@else
										<span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-lightgray text-muted text-[11px] font-bold">No access</span>
									@endif
								</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-end gap-1">
										<a href="{{ route('admin.users.edit', $user) }}"
										   class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-blue hover:border-blue" title="Edit">
											<x-icon name="pencil" class="w-4 h-4" />
										</a>
										@if($user->id !== auth()->id())
											<form method="POST" action="{{ route('admin.users.destroy', $user) }}"
											      onsubmit="return confirm('Remove {{ $user->name }} from the admin panel?');">
												@csrf
												@method('DELETE')
												<button type="submit"
												        class="w-8 h-8 rounded-[4px] border border-line flex items-center justify-center text-muted hover:text-red-600 hover:border-red-300 cursor-pointer" title="Delete">
													<x-icon name="trash" class="w-4 h-4" />
												</button>
											</form>
										@endif
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>

	<div class="mt-5">{{ $users->links('pagination.default') }}</div>
</x-layouts.admin>
