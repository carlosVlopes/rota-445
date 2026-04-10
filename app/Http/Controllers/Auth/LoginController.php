<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Exibe a tela de login com teclado numérico para PIN.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Processa o login.
     *
     * - Garçom e caixa: autenticam apenas com PIN (4 dígitos)
     * - Admin: autentica com email + senha
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'pin'      => ['nullable', 'digits_between:4,6'],
            'email'    => ['nullable', 'email'],
            'password' => ['nullable', 'string'],
        ]);

        // Login por PIN (garçom / caixa)
        if ($request->filled('pin')) {
            return $this->loginByPin($request);
        }

        // Login por email + senha (admin)
        if ($request->filled('email') && $request->filled('password')) {
            return $this->loginByPassword($request);
        }

        return back()->withErrors(['pin' => 'Informe um PIN ou e-mail e senha.']);
    }

    private function loginByPin(Request $request): RedirectResponse
    {
        $user = User::where('pin', $request->pin)
            ->whereIn('role', ['waiter', 'cashier'])
            ->where('active', true)
            ->first();

        if (! $user) {
            return back()->withErrors(['pin' => 'PIN inválido ou usuário inativo.']);
        }

        Auth::login($user, remember: false);
        $request->session()->regenerate();

        return $this->redirectByRole($user->role);
    }

    private function loginByPassword(Request $request): RedirectResponse
    {
        if (! Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => true])) {
            return back()->withErrors(['email' => 'E-mail ou senha incorretos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user()->role);
    }

    /**
     * Redireciona para a área correta após o login.
     */
    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'cashier' => redirect()->route('cashier.index'),
            'waiter'  => redirect()->route('waiter.tables'),
            default   => redirect('/'),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
