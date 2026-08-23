<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::where('role', 'worker')->get();
        return view('manager.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        return view('manager.users.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'worker',
        ]);

        return redirect()->route('manager.users.index');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('manager.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('manager.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->only('name', 'email');
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('manager.users.index');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route('manager.users.index');
    }
}