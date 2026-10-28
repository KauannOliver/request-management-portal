<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etapa;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cadastrar as etapas obrigatórias
        $etapas = [
            ['nome' => 'Ideia Registrada', 'ordem' => 1, 'ativo' => true],
            ['nome' => 'Levantamento', 'ordem' => 2, 'ativo' => true],
            ['nome' => 'Processo de Software', 'ordem' => 3, 'ativo' => true],
            ['nome' => 'Homologação', 'ordem' => 4, 'ativo' => true],
            ['nome' => 'Aplicação', 'ordem' => 5, 'ativo' => true],
            ['nome' => 'Concluído', 'ordem' => 6, 'ativo' => true],
        ];

        foreach ($etapas as $etapa) {
            Etapa::firstOrCreate(
                ['nome' => $etapa['nome']],
                ['ordem' => $etapa['ordem'], 'ativo' => $etapa['ativo']]
            );
        }

        // 2. Cadastrar o usuário recurso inicial de forma segura
        $name = env('INITIAL_RESOURCE_NAME');
        $email = env('INITIAL_RESOURCE_EMAIL');
        $password = env('INITIAL_RESOURCE_PASSWORD');

        if (empty($name) || empty($email) || empty($password)) {
            throw new \RuntimeException(
                "As variáveis de ambiente do recurso inicial (INITIAL_RESOURCE_NAME, " .
                "INITIAL_RESOURCE_EMAIL, INITIAL_RESOURCE_PASSWORD) não foram definidas no arquivo .env."
            );
        }

        // Cria o usuário se ele não existir
        Usuario::firstOrCreate(
            ['email' => $email],
            [
                'nome' => $name,
                'password' => Hash::make($password),
                'perfil' => 'recurso',
                'ativo' => true,
            ]
        );
    }
}
