<?php

namespace App\Actions\Usuario;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RedefinirSenhaAction
{
    /**
     * Executa a redefinição segura de senha com hash de proteção.
     */
    public function execute(Usuario $usuario, string $newPassword): void
    {
        DB::transaction(function () use ($usuario, $newPassword) {
            $usuario->update([
                'password' => Hash::make($newPassword)
            ]);
        });
    }
}
