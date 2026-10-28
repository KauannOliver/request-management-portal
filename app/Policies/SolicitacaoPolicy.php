<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Solicitacao;

class SolicitacaoPolicy
{
    /**
     * Determina se o usuário pode listar as solicitações.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true; // Gestor e Recurso podem listar
    }

    /**
     * Determina se o usuário pode detalhar uma solicitação.
     */
    public function view(Usuario $usuario, Solicitacao $solicitacao): bool
    {
        return true; // Gestor e Recurso podem detalhar
    }

    /**
     * Determina se o usuário pode cadastrar solicitações.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->isRecurso();
    }

    /**
     * Determina se o usuário pode editar a solicitação.
     */
    public function update(Usuario $usuario, Solicitacao $solicitacao): bool
    {
        return $usuario->isRecurso();
    }

    /**
     * Determina se o usuário pode deletar logicamente a solicitação.
     */
    public function delete(Usuario $usuario, Solicitacao $solicitacao): bool
    {
        return $usuario->isRecurso();
    }

    /**
     * Determina se o usuário pode restaurar a solicitação deletada.
     */
    public function restore(Usuario $usuario, Solicitacao $solicitacao): bool
    {
        return $usuario->isRecurso();
    }
}
