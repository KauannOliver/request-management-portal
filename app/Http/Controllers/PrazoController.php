<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitacao;
use App\Models\Etapa;
use App\Models\Usuario;
use Carbon\Carbon;

class PrazoController extends Controller
{
    /**
     * Exibe o painel de visualização de prazos.
     */
    public function index(Request $request)
    {
        $query = Solicitacao::with(['etapa', 'recursos']);

        // Aplicar filtros de base
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }
        if ($request->filled('etapa_id')) {
            $query->where('etapa_id', $request->input('etapa_id'));
        }
        if ($request->filled('recurso_id')) {
            $query->whereHas('recursos', function($q) use ($request) {
                $q->where('usuarios.id', $request->input('recurso_id'));
            });
        }
        if ($request->filled('periodo_de')) {
            $query->whereDate('data_prazo', '>=', $request->input('periodo_de'));
        }
        if ($request->filled('periodo_ate')) {
            $query->whereDate('data_prazo', '<=', $request->input('periodo_ate'));
        }

        // Filtro específico de vencimentos e situações
        if ($request->filled('situacao')) {
            $situacao = $request->input('situacao');
            $hoje = Carbon::today();

            if ($situacao === 'atrasadas') {
                $query->where('percentual_conclusao', '<', 100)
                      ->where('data_prazo', '<', now());
            } elseif ($situacao === 'hoje') {
                $query->where('percentual_conclusao', '<', 100)
                      ->whereDate('data_prazo', $hoje);
            } elseif ($situacao === 'amanha') {
                $query->where('percentual_conclusao', '<', 100)
                      ->whereDate('data_prazo', $hoje->copy()->addDay());
            } elseif ($situacao === 'proximos_7_dias') {
                $query->where('percentual_conclusao', '<', 100)
                      ->whereBetween('data_prazo', [$hoje, $hoje->copy()->addDays(7)]);
            } elseif ($situacao === 'concluidas_no_prazo') {
                $query->where('percentual_conclusao', 100)
                      ->where(function($q) {
                          $q->whereNull('data_entrega')
                            ->orWhereColumn('data_entrega', '<=', 'data_prazo');
                      });
            } elseif ($situacao === 'concluidas_com_atraso') {
                $query->where('percentual_conclusao', 100)
                      ->whereNotNull('data_entrega')
                      ->whereColumn('data_entrega', '>', 'data_prazo');
            }
        }

        // Paginação dinâmica (10, 25 ou 40 itens)
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 40])) {
            $perPage = 10;
        }

        $solicitacoes = $query->orderBy('data_prazo', 'asc')->paginate($perPage)->withQueryString();

        // Calcular contadores globais respeitando os filtros básicos para consistência
        $baseQuery = Solicitacao::query();
        if ($request->filled('tipo')) {
            $baseQuery->where('tipo', $request->input('tipo'));
        }
        if ($request->filled('etapa_id')) {
            $baseQuery->where('etapa_id', $request->input('etapa_id'));
        }
        if ($request->filled('recurso_id')) {
            $baseQuery->whereHas('recursos', function($q) use ($request) {
                $q->where('usuarios.id', $request->input('recurso_id'));
            });
        }

        $hoje = Carbon::today();

        $cntAtrasadas = (clone $baseQuery)->where('percentual_conclusao', '<', 100)->where('data_prazo', '<', now())->count();
        $cntHoje = (clone $baseQuery)->where('percentual_conclusao', '<', 100)->whereDate('data_prazo', $hoje)->count();
        $cntAmanha = (clone $baseQuery)->where('percentual_conclusao', '<', 100)->whereDate('data_prazo', $hoje->copy()->addDay())->count();
        $cnt7dias = (clone $baseQuery)->where('percentual_conclusao', '<', 100)->whereBetween('data_prazo', [$hoje, $hoje->copy()->addDays(7)])->count();
        
        $cntNoPrazo = (clone $baseQuery)->where('percentual_conclusao', 100)
            ->where(function($q) {
                $q->whereNull('data_entrega')
                  ->orWhereColumn('data_entrega', '<=', 'data_prazo');
            })->count();
            
        $cntComAtraso = (clone $baseQuery)->where('percentual_conclusao', 100)
            ->whereNotNull('data_entrega')
            ->whereColumn('data_entrega', '>', 'data_prazo')
            ->count();

        // Dados para os filtros
        $todasEtapas = Etapa::orderBy('ordem')->get();
        $todosRecursos = Usuario::recurso()->ativo()->orderBy('nome')->get();

        return view('prazos.index', compact(
            'solicitacoes', 'todasEtapas', 'todosRecursos',
            'cntAtrasadas', 'cntHoje', 'cntAmanha', 'cnt7dias', 'cntNoPrazo', 'cntComAtraso'
        ));
    }
}
