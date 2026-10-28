<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MinhaContaController extends Controller
{
    /**
     * Exibe o formulário de minha conta.
     */
    public function index()
    {
        return view('minha-conta.index', [
            'usuario' => Auth::user()
        ]);
    }

    /**
     * Atualiza as informações da conta do próprio usuário logado.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        $dadosValidados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $usuario->id,
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $usuario->nome = $dadosValidados['nome'];
        $usuario->email = $dadosValidados['email'];

        if (!empty($dadosValidados['password'])) {
            $usuario->password = Hash::make($dadosValidados['password']);
        }

        $usuario->save(); // O observer UsuarioObserver fará a auditoria automaticamente

        return redirect()->route('minha-conta.index')->with('success', 'Dados da sua conta atualizados com sucesso!');
    }
}
