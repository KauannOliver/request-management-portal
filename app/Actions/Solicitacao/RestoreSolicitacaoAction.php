<?php

namespace App\Actions\Solicitacao;

use App\Models\Solicitacao;
use Illuminate\Support\Facades\DB;

class RestoreSolicitacaoAction
{
    /**
     * Restaura uma solicitação apagada e limpa os campos de auditoria de exclusão.
     */
    public function execute(Solicitacao $solicitacao): void
    {
        DB::transaction(function () use ($solicitacao) {
            $solicitacao->update([
                'excluido_por_id' => null,
                'motivo_exclusao' => null
            ]);
            $solicitacao->restore();
        });
    }
}
