<?php

namespace App\Actions\Solicitacao;

use App\Models\Solicitacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteSolicitacaoAction
{
    /**
     * Executa a exclusão lógica informando o motivo e registrando o usuário autor da ação.
     */
    public function execute(Solicitacao $solicitacao, string $motivoExclusao): void
    {
        DB::transaction(function () use ($solicitacao, $motivoExclusao) {
            $solicitacao->update([
                'excluido_por_id' => Auth::id(),
                'motivo_exclusao' => $motivoExclusao
            ]);
            $solicitacao->delete();
        });
    }
}
