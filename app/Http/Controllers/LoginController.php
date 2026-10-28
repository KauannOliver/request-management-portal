<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Services\AuditoriaService;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Exibe a tela de login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Processa a autenticação do usuário.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('email', $credentials['email'])->first();

        // Se o usuário existe e está inativo, bloqueia antes mesmo de validar a senha
        if ($usuario && !$usuario->ativo) {
            // Registra auditoria de falha de login (inativo)
            AuditoriaService::registrar(
                'Falha de login relevante',
                'seguranca',
                $usuario->id,
                "Tentativa de login bloqueada para o usuário inativo: {$usuario->nome} ({$usuario->email})."
            );

            throw ValidationException::withMessages([
                'email' => 'Sua conta está desativada. Entre em contato com um recurso responsável.',
            ]);
        }

        // Tenta autenticar
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $usuarioAutenticado = Auth::user();
            
            // Atualiza último acesso
            $usuarioAutenticado->update([
                'ultimo_acesso_em' => now()
            ]);

            // Registra login no log de auditoria
            AuditoriaService::registrar(
                'Login',
                'usuarios',
                $usuarioAutenticado->id,
                "Usuário {$usuarioAutenticado->nome} realizou login com sucesso."
            );

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        // Se a autenticação falhou e o usuário existe
        if ($usuario) {
            AuditoriaService::registrar(
                'Falha de login relevante',
                'seguranca',
                $usuario->id,
                "Tentativa de login com senha incorreta para o usuário: {$usuario->nome} ({$usuario->email})."
            );
        } else {
            AuditoriaService::registrar(
                'Falha de login relevante',
                'seguranca',
                null,
                "Tentativa de login para e-mail não cadastrado: {$credentials['email']}."
            );
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Processa o logout do usuário.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            
            AuditoriaService::registrar(
                'Logout',
                'usuarios',
                $usuario->id,
                "Usuário {$usuario->nome} realizou logout."
            );

            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
