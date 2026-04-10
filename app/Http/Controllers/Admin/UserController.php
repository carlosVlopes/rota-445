<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('status')) {
            $query->where('active', $request->get('status') === 'active');
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'pin'      => ['nullable', 'digits:4', 'unique:users,pin'],
            'role'     => ['required', 'in:admin,waiter,cashier'],
            'active'   => ['boolean'],
            'password' => ['nullable', 'required_if:role,admin', Password::defaults()],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'] ?? null,
            'pin'      => $validated['pin'] ?? null,
            'role'     => $validated['role'],
            'active'   => $request->boolean('active', true),
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : null,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $usuario): View
    {
        return view('admin.users.edit', ['user' => $usuario]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'pin'      => ['nullable', 'digits:4', 'unique:users,pin,' . $usuario->id],
            'role'     => ['required', 'in:admin,waiter,cashier'],
            'active'   => ['boolean'],
            'password' => ['nullable', Password::defaults()],
        ]);

        $data = [
            'name'   => $validated['name'],
            'email'  => $validated['email'] ?? null,
            'pin'    => $validated['pin'] ?? $usuario->pin,
            'role'   => $validated['role'],
            'active' => $request->boolean('active', true),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode remover seu próprio usuário.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário removido com sucesso.');
    }
}
