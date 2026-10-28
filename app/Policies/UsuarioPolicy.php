<?php

namespace App\Policies;

use App\Models\Usuario;

class UsuarioPolicy
{
    /**
     * Determina se o usuário pode gerenciar outros usuários.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->isRecurso();
    }

    /**
     * Determina se o usuário pode visualizar detalhes de um usuário.
     */
    public function view(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->isRecurso();
    }

    /**
     * Determina se o usuário pode criar usuários.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->isRecurso();
    }

    /**
     * Determina se o usuário pode atualizar o usuário.
     */
    public function update(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->isRecurso();
    }

    /**
     * Determina se o usuário pode deletar o usuário.
     */
    public function delete(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->isRecurso();
    }
}
