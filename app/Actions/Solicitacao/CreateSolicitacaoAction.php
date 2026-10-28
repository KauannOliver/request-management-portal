<?php

namespace App\Actions\Solicitacao;

use App\Models\Solicitacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateSolicitacaoAction
{
    /**
     * Executa a criação de uma solicitação sob uma transação DB.
     */
    public function execute(array $data): Solicitacao
    {
        return DB::transaction(function () use ($data) {
            $dataAbertura = Carbon::parse($data['data_abertura']);
            $dataPrazo = ($data['tipo'] === 'SD' && !empty($data['data_prazo']))
                ? Carbon::parse($data['data_prazo'])
                : null;

            $solicitacao = Solicitacao::create([
                'numero' => $data['numero'],
                'tipo' => $data['tipo'],
                'nome_solicitante' => $data['nome_solicitante'],
                'descricao' => $data['descricao'],
                'observacoes' => $data['observacoes'] ?? null,
                'etapa_id' => $data['etapa_id'],
                'percentual_conclusao' => $data['percentual_conclusao'],
                'data_abertura' => $dataAbertura,
                'data_prazo' => $dataPrazo,
                'criado_por_id' => Auth::id(),
            ]);

            // Associa recursos
            if (!empty($data['recursos'])) {
                $solicitacao->recursos()->sync(array_fill_keys($data['recursos'], [
                    'atribuido_por_id' => Auth::id(),
                    'atribuido_em' => now()
                ]));
            }

            return $solicitacao;
        });
    }
}
