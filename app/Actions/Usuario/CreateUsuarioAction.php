<?php

namespace App\Actions\Usuario;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateUsuarioAction
{
    /**
     * Executa a criação de um usuário criptografando sua senha de acesso.
     */
    public function execute(array $data): Usuario
    {
        return DB::transaction(function () use ($data) {
            return Usuario::create([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'perfil' => $data['perfil'],
                'ativo' => (bool) $data['ativo']
            ]);
        });
    }
}
