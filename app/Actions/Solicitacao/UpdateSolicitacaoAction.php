<?php

namespace App\Actions\Solicitacao;

use App\Models\Solicitacao;
use App\Models\Usuario;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateSolicitacaoAction
{
    /**
     * Executa a atualização da solicitação sob uma transação DB, incluindo auditoria dos recursos.
     */
    public function execute(Solicitacao $solicitacao, array $data): Solicitacao
    {
        return DB::transaction(function () use ($solicitacao, $data) {
            $dataAbertura = Carbon::parse($data['data_abertura']);
            $dataPrazo = ($data['tipo'] === 'SD' && !empty($data['data_prazo']))
                ? Carbon::parse($data['data_prazo'])
                : null;

            $recursosNovos = $data['recursos'] ?? [];
            $recursosAtuais = $solicitacao->recursos->pluck('id')->toArray();

            $incluidos = array_diff($recursosNovos, $recursosAtuais);
            $removidos = array_diff($recursosAtuais, $recursosNovos);

            $solicitacao->fill([
                'numero' => $data['numero'],
                'tipo' => $data['tipo'],
                'nome_solicitante' => $data['nome_solicitante'],
                'descricao' => $data['descricao'],
                'observacoes' => $data['observacoes'] ?? null,
                'etapa_id' => $data['etapa_id'],
                'percentual_conclusao' => $data['percentual_conclusao'],
                'data_abertura' => $dataAbertura,
                'data_prazo' => $dataPrazo,
                'atualizado_por_id' => Auth::id(),
            ]);

            // Se for enviado data_entrega manualmente (para quando já estava 100%)
            if (isset($data['data_entrega']) && $data['percentual_conclusao'] == 100) {
                $solicitacao->data_entrega = $data['data_entrega'] ? Carbon::parse($data['data_entrega']) : null;
            }

            $solicitacao->save();

            // Sincronizar recursos na tabela pivot
            $solicitacao->recursos()->sync(array_fill_keys($recursosNovos, [
                'atribuido_por_id' => Auth::id(),
                'atribuido_em' => now()
            ]));

            // Registrar log das inclusões
            foreach ($incluidos as $userId) {
                $user = Usuario::find($userId);
                AuditoriaService::registrar(
                    'Inclusão de recurso',
                    'solicitacoes',
                    $solicitacao->id,
                    "O recurso {$user->nome} foi atribuído como responsável pela solicitação {$solicitacao->identificacao}.",
                    null,
                    ['usuario_id' => $user->id, 'nome' => $user->nome]
                );
            }

            // Registrar log das remoções
            foreach ($removidos as $userId) {
                $user = Usuario::find($userId);
                AuditoriaService::registrar(
                    'Remoção de recurso',
                    'solicitacoes',
                    $solicitacao->id,
                    "O recurso {$user->nome} foi removido da responsabilidade pela solicitação {$solicitacao->identificacao}.",
                    ['usuario_id' => $user->id, 'nome' => $user->nome],
                    null
                );
            }

            return $solicitacao;
        });
    }
}
