<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(['role' => User::ROLE_MANAGER]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // role is deliberately not in User::$fillable — set it explicitly, same as the seeder.
        $user->forceFill(['role' => $data['role']])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Team member added.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        if ($this->wouldRemoveLastAdmin($user, $data['role'])) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'At least one Admin account must remain — change someone else to Admin first.']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            // The 'hashed' cast on User::password hashes this automatically (see the model).
            $user->password = $data['password'];
        }

        $user->save();
        $user->forceFill(['role' => $data['role']])->save();

        $message = $user->id === $request->user()->id
            ? 'Your account was updated.'
            : 'Team member updated.';

        return redirect()->route('admin.users.index')->with('status', $message);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account while logged in.']);
        }

        if ($this->wouldRemoveLastAdmin($user, null)) {
            return back()->withErrors(['user' => 'At least one Admin account must remain — cannot delete the last Admin.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Team member removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', 'string', 'min:8'],
            'role' => ['required', Rule::in(array_keys(User::roleOptions()))],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    /**
     * True if setting $user's role to $newRole (null = deleting) would leave zero Admins.
     */
    private function wouldRemoveLastAdmin(User $user, ?string $newRole): bool
    {
        if ($newRole === User::ROLE_ADMIN || ! $user->isAdmin()) {
            return false;
        }

        $otherAdmins = User::where('role', User::ROLE_ADMIN)->where('id', '!=', $user->id)->count();

        return $otherAdmins === 0;
    }
}
