<?php

namespace App\Actions\Usuario;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class UpdateUsuarioAction
{
    /**
     * Atualiza dados de e-mail, nome, perfil e atividade do usuário.
     */
    public function execute(Usuario $usuario, array $data): Usuario
    {
        return DB::transaction(function () use ($usuario, $data) {
            $usuario->update([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'perfil' => $data['perfil'],
                'ativo' => (bool) $data['ativo']
            ]);
            return $usuario;
        });
    }
}
