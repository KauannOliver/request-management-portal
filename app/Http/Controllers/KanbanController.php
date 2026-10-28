<?php

namespace App\Http\Controllers;

use App\Models\Etapa;
use App\Models\Solicitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class KanbanController extends Controller
{
    /**
     * Exibe o Quadro Kanban agrupado por Etapas.
     */
    public function index()
    {
        $etapas = Etapa::orderBy('ordem')->get();
        
        // Carrega todas as solicitações não arquivadas (ativas) com seus respectivos responsáveis
        $solicitacoes = Solicitacao::with('recursos', 'etapa')->get();

        // Agrupa solicitações por etapa_id para facilitar o laço nas colunas do Blade
        $solicitacoesPorEtapa = $solicitacoes->groupBy('etapa_id');

        return view('kanban.index', compact('etapas', 'solicitacoesPorEtapa'));
    }

    /**
     * Atualiza a etapa de uma solicitação via chamada API (Drag and Drop).
     */
    public function updateEtapa(Request $request, $id)
    {
        $solicitacao = Solicitacao::findOrFail($id);
        
        // Valida se o usuário tem permissão de escrita
        Gate::authorize('update', $solicitacao);

        $request->validate([
            'etapa_id' => 'required|exists:etapas,id'
        ]);

        $solicitacao->etapa_id = $request->etapa_id;
        $solicitacao->save(); // O SolicitacaoObserver cuidará de disparar o log de transição de etapa

        return response()->json([
            'success' => true,
            'message' => "Solicitação {$solicitacao->identificacao} movida com sucesso para a nova etapa."
        ]);
    }
}
