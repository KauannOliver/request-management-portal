<?php

namespace App\Observers;

use App\Models\Usuario;
use App\Services\AuditoriaService;

class UsuarioObserver
{
    /**
     * Registra auditoria ao cadastrar um usuário.
     */
    public function created(Usuario $usuario): void
    {
        AuditoriaService::registrar(
            'Cadastro de usuário',
            'usuarios',
            $usuario->id,
            "O usuário {$usuario->nome} ({$usuario->email}) foi cadastrado com perfil {$usuario->perfil}.",
            null,
            $usuario->toArray()
        );
    }

    /**
     * Registra auditoria ao atualizar dados do usuário.
     */
    public function updated(Usuario $usuario): void
    {
        $dirty = $usuario->getDirty();
        
        // Se a única alteração foi o último acesso, não registramos para evitar sobrecarga de logs
        if (count($dirty) === 1 && isset($dirty['ultimo_acesso_em'])) {
            return;
        }

        $original = array_intersect_key($usuario->getOriginal(), $dirty);
        
        $acao = 'Alteração de usuário';
        $descricao = "O usuário {$usuario->nome} teve seus dados atualizados.";
        
        if (isset($dirty['ativo'])) {
            $status = $dirty['ativo'] ? 'ativado' : 'desativado';
            $acao = 'Ativação ou desativação de usuário';
            $descricao = "O usuário {$usuario->nome} foi {$status} no sistema.";
        }

        AuditoriaService::registrar(
            $acao,
            'usuarios',
            $usuario->id,
            $descricao,
            $original,
            $dirty
        );
    }
}
