<?php

namespace App\Observers;

use App\Models\Solicitacao;
use App\Models\Etapa;
use App\Services\AuditoriaService;
use Carbon\Carbon;

class SolicitacaoObserver
{
    /**
     * Registra auditoria ao criar uma solicitação.
     */
    public function created(Solicitacao $solicitacao): void
    {
        AuditoriaService::registrar(
            'Cadastro de solicitação',
            'solicitacoes',
            $solicitacao->id,
            "A solicitação {$solicitacao->identificacao} foi criada pelo recurso.",
            null,
            $solicitacao->toArray()
        );
    }

    /**
     * Registra auditoria ao atualizar dados da solicitação.
     */
    public function updated(Solicitacao $solicitacao): void
    {
        $dirty = $solicitacao->getDirty();
        $original = array_intersect_key($solicitacao->getOriginal(), $dirty);

        // Se deleted_at mudou, trata-se de exclusão lógica ou restauração
        if (isset($dirty['deleted_at'])) {
            if (!is_null($dirty['deleted_at']) && is_null($original['deleted_at'])) {
                // Exclusão lógica
                AuditoriaService::registrar(
                    'Exclusão lógica',
                    'solicitacoes',
                    $solicitacao->id,
                    "A solicitação {$solicitacao->identificacao} foi excluída logicamente. Motivo: " . ($solicitacao->motivo_exclusao ?? 'Não informado') . ".",
                    ['deleted_at' => null],
                    [
                        'deleted_at' => $dirty['deleted_at'],
                        'motivo_exclusao' => $solicitacao->motivo_exclusao,
                        'excluido_por_id' => $solicitacao->excluido_por_id
                    ]
                );
                return;
            }
            
            if (is_null($dirty['deleted_at']) && !is_null($original['deleted_at'])) {
                // Restauração
                AuditoriaService::registrar(
                    'Restauração',
                    'solicitacoes',
                    $solicitacao->id,
                    "A solicitação {$solicitacao->identificacao} foi restaurada no sistema.",
                    ['deleted_at' => $original['deleted_at']],
                    ['deleted_at' => null]
                );
                return;
            }
        }

        // Trata cada campo relevante alterado de forma individual
        foreach ($dirty as $campo => $valor) {
            $valAntigo = $original[$campo] ?? null;

            if ($campo === 'etapa_id') {
                $etapaAntiga = Etapa::find($valAntigo);
                $etapaNova = Etapa::find($valor);
                
                $ordemAntiga = $etapaAntiga?->ordem ?? 0;
                $ordemNova = $etapaNova?->ordem ?? 0;
                
                $movimento = $ordemNova < $ordemAntiga ? 'retrocedeu' : 'avançou';
                $etapaAntigaNome = $etapaAntiga?->nome ?? "Desconhecida (#{$valAntigo})";
                $etapaNovaNome = $etapaNova?->nome ?? "Desconhecida (#{$valor})";

                AuditoriaService::registrar(
                    'Mudança de etapa',
                    'solicitacoes',
                    $solicitacao->id,
                    "A solicitação {$solicitacao->identificacao} {$movimento} da etapa '{$etapaAntigaNome}' para '{$etapaNovaNome}'.",
                    ['etapa_id' => $valAntigo],
                    ['etapa_id' => $valor]
                );
            }
            
            elseif ($campo === 'percentual_conclusao') {
                AuditoriaService::registrar(
                    'Mudança de percentual',
                    'solicitacoes',
                    $solicitacao->id,
                    "O percentual de conclusão da solicitação {$solicitacao->identificacao} mudou de {$valAntigo}% para {$valor}%.",
                    ['percentual_conclusao' => $valAntigo],
                    ['percentual_conclusao' => $valor]
                );
            }
            
            elseif ($campo === 'data_prazo') {
                $prazoAntigo = $valAntigo ? Carbon::parse($valAntigo)->format('d/m/Y H:i') : 'Não definido';
                $prazoNovo = $valor ? Carbon::parse($valor)->format('d/m/Y H:i') : 'Não definido';

                AuditoriaService::registrar(
                    'Mudança de prazo',
                    'solicitacoes',
                    $solicitacao->id,
                    "O prazo da solicitação {$solicitacao->identificacao} mudou de {$prazoAntigo} para {$prazoNovo}.",
                    ['data_prazo' => $valAntigo],
                    ['data_prazo' => $valor]
                );
            }
            
            elseif ($campo === 'data_entrega') {
                $entregaAntiga = $valAntigo ? Carbon::parse($valAntigo)->format('d/m/Y H:i') : 'Vazia';
                $entregaNova = $valor ? Carbon::parse($valor)->format('d/m/Y H:i') : 'Vazia';
                
                $descricao = "A data de entrega da solicitação {$solicitacao->identificacao} foi ajustada de {$entregaAntiga} para {$entregaNova}.";
                if (is_null($valor)) {
                    $descricao = "A solicitação {$solicitacao->identificacao} foi reaberta (entrega removida).";
                }

                AuditoriaService::registrar(
                    'Mudança de data de entrega',
                    'solicitacoes',
                    $solicitacao->id,
                    $descricao,
                    ['data_entrega' => $valAntigo],
                    ['data_entrega' => $valor]
                );
            }
        }

        // Outros campos cadastrais gerais (número, tipo, descrição, solicitante, etc.)
        $camposEsquecidos = ['etapa_id', 'percentual_conclusao', 'data_prazo', 'data_entrega', 'deleted_at', 'updated_at', 'atualizado_por_id'];
        $outrasChaves = array_diff_key($dirty, array_flip($camposEsquecidos));
        
        if (count($outrasChaves) > 0) {
            $origOutras = array_intersect_key($original, $outrasChaves);
            AuditoriaService::registrar(
                'Alteração de solicitação',
                'solicitacoes',
                $solicitacao->id,
                "Os dados cadastrais gerais da solicitação {$solicitacao->identificacao} foram editados.",
                $origOutras,
                $outrasChaves
            );
        }
    }
}
