<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => User::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('users.index')->with('success', 'Utilisateur cree.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => User::ROLES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $user->update($this->validated($request, $user));

        return redirect()->route('users.index')->with('success', 'Utilisateur modifie.');
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprime.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
        ];

        $data = $request->validate($rules);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }
}
