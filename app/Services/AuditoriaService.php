<?php

namespace App\Services;

use App\Models\LogAuditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditoriaService
{
    /**
     * Registra uma ação de auditoria no banco de dados.
     */
    public static function registrar(
        string $acao,
        string $entidade,
        ?int $entidadeId,
        string $descricao,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null
    ): void {
        // Nunca armazenar senhas ou tokens
        $camposSensiveis = ['password', 'remember_token', 'rememberToken'];
        
        if (is_array($dadosAnteriores)) {
            $dadosAnteriores = array_diff_key($dadosAnteriores, array_flip($camposSensiveis));
        }
        
        if (is_array($dadosNovos)) {
            $dadosNovos = array_diff_key($dadosNovos, array_flip($camposSensiveis));
        }

        LogAuditoria::create([
            'usuario_id' => Auth::id(),
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'endereco_ip' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::header('User-Agent') ?? 'System/CLI',
            'created_at' => now(),
        ]);
    }
}
