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
            'user' => new User(['is_admin' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
            'is_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // is_admin is deliberately not in User::$fillable — set it explicitly, same as the seeder.
        $user->forceFill(['is_admin' => $request->boolean('is_admin')])->save();

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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'string', 'min:8'],
            'is_admin' => ['boolean'],
        ]);

        if ($this->wouldRemoveLastAdmin($user, $request->boolean('is_admin'))) {
            return back()
                ->withInput()
                ->withErrors(['is_admin' => 'At least one admin account must remain — cannot remove admin access here.']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            // The 'hashed' cast on User::password hashes this automatically (see the model).
            $user->password = $data['password'];
        }

        $user->save();
        $user->forceFill(['is_admin' => $request->boolean('is_admin')])->save();

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

        if ($this->wouldRemoveLastAdmin($user, false)) {
            return back()->withErrors(['user' => 'At least one admin account must remain — cannot delete the last admin.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Team member removed.');
    }

    /**
     * True if setting $user's admin flag to $newValue would leave zero admins.
     */
    private function wouldRemoveLastAdmin(User $user, bool $newValue): bool
    {
        if ($newValue || ! $user->isAdmin()) {
            return false;
        }

        $otherAdmins = User::where('is_admin', true)->where('id', '!=', $user->id)->count();

        return $otherAdmins === 0;
    }
}
