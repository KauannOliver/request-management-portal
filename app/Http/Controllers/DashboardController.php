<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitacao;
use App\Models\Etapa;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Exibe a página principal do dashboard.
     */
    public function index(Request $request)
    {
        // 1. Obter query base com filtros aplicados
        $queryBase = $this->aplicarFiltros(Solicitacao::query(), $request);

        // 2. Calcular os cards de estatísticas (baseados nos filtros globais)
        $totalSolicitacoes = (clone $queryBase)->count();
        $totalSaus = (clone $queryBase)->where('tipo', 'SAU')->count();
        $totalSds = (clone $queryBase)->where('tipo', 'SD')->count();
        
        $concluidas = (clone $queryBase)->where('percentual_conclusao', 100)->count();
        $naoIniciadas = (clone $queryBase)->where('percentual_conclusao', 0)->count();
        
        // Atrasadas: percentual < 100 e data_prazo vencida
        $atrasadas = (clone $queryBase)
            ->where('percentual_conclusao', '<', 100)
            ->where('data_prazo', '<', now())
            ->count();
            
        $emAndamento = $totalSolicitacoes - $concluidas - $naoIniciadas; // percentual de 1 a 99

        $percentualMedio = (clone $queryBase)->avg('percentual_conclusao') ?? 0;
        
        // Entregas previstas nos próximos 7 dias (exclusivo para as não concluídas)
        $entregasProximas = (clone $queryBase)
            ->where('percentual_conclusao', '<', 100)
            ->whereBetween('data_prazo', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->count();

        // 3. Montar dados para Gráfico 1: Solicitações por Etapa
        $etapasTotais = Etapa::orderBy('ordem')->get();
        $graficoEtapas = [
            'labels' => [],
            'data' => []
        ];
        foreach ($etapasTotais as $etapa) {
            $graficoEtapas['labels'][] = $etapa->nome;
            $graficoEtapas['data'][] = (clone $queryBase)->where('etapa_id', $etapa->id)->count();
        }

        // Gráfico 2: Solicitações por Tipo
        $graficoTipos = [
            'labels' => ['SAU', 'SD'],
            'data' => [$totalSaus, $totalSds]
        ];

        // Gráfico 3: Distribuição por faixa de conclusão
        $faixas = [
            '0%' => [0, 0],
            '1% a 25%' => [1, 25],
            '26% a 50%' => [26, 50],
            '51% a 75%' => [51, 75],
            '76% a 99%' => [76, 99],
            '100%' => [100, 100]
        ];
        $graficoFaixas = [
            'labels' => array_keys($faixas),
            'data' => []
        ];
        foreach ($faixas as $faixa => $limites) {
            $graficoFaixas['data'][] = (clone $queryBase)
                ->whereBetween('percentual_conclusao', $limites)
                ->count();
        }

        // Gráfico 4: Quantidade por Recurso (Top Recursos com mais solicitações atribuídas)
        // Como o banco é SQLite, buscamos da pivot
        $recursosDados = DB::table('solicitacao_recursos')
            ->join('usuarios', 'solicitacao_recursos.usuario_id', '=', 'usuarios.id')
            ->whereIn('solicitacao_recursos.solicitacao_id', (clone $queryBase)->pluck('id'))
            ->select('usuarios.nome', DB::raw('count(solicitacao_recursos.solicitacao_id) as total'))
            ->groupBy('usuarios.nome')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $graficoRecursos = [
            'labels' => $recursosDados->pluck('nome')->toArray(),
            'data' => $recursosDados->pluck('total')->toArray()
        ];

        // Gráfico 5: Abertas e Concluídas por mês (Últimos 6 meses)
        $graficoMeses = [
            'labels' => [],
            'abertas' => [],
            'concluidas' => []
        ];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $nomeMes = $mes->translatedFormat('F/y'); // Ex: Julho/26
            $graficoMeses['labels'][] = ucfirst($nomeMes);
            
            // Abertas no mês
            $abertasNoMes = (clone $queryBase)
                ->whereMonth('data_abertura', $mes->month)
                ->whereYear('data_abertura', $mes->year)
                ->count();
            $graficoMeses['abertas'][] = $abertasNoMes;

            // Concluídas no mês
            $concluidasNoMes = (clone $queryBase)
                ->where('percentual_conclusao', 100)
                ->whereMonth('data_entrega', $mes->month)
                ->whereYear('data_entrega', $mes->year)
                ->count();
            $graficoMeses['concluidas'][] = $concluidasNoMes;
        }

        // Gráfico 6: Prazos e Entregas (Evolução semanal de solicitações entregues no prazo vs entregues com atraso)
        $concluidasGerais = (clone $queryBase)->where('percentual_conclusao', 100)->get();
        $noPrazo = 0;
        $comAtraso = 0;
        foreach ($concluidasGerais as $sol) {
            if ($sol->data_entrega && $sol->data_prazo) {
                if ($sol->data_entrega->gt($sol->data_prazo)) {
                    $comAtraso++;
                } else {
                    $noPrazo++;
                }
            } else {
                $noPrazo++;
            }
        }
        $graficoPrazosEntregas = [
            'labels' => ['No Prazo', 'Com Atraso'],
            'data' => [$noPrazo, $comAtraso]
        ];

        // 4. Painel de Atenção: Solicitações que exigem atenção
        // Regras:
        // - Atrasadas (prioridade 1)
        // - Prazo nas próximas 24 horas (prioridade 2)
        // - Baixo progresso (<25%) e prazo próximo (próximos 3 dias)
        // - Reabertas
        $painelAtencao = (clone $queryBase)
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->where('percentual_conclusao', '<', 100)
                        ->where('data_prazo', '<', now()); // Atrasada
                })
                ->orWhere(function($sub) {
                    $sub->where('percentual_conclusao', '<', 100)
                        ->whereBetween('data_prazo', [now(), now()->addHours(24)]); // Próximas 24h
                })
                ->orWhere(function($sub) {
                    $sub->where('percentual_conclusao', '<', 25)
                        ->whereBetween('data_prazo', [now(), now()->addDays(3)]); // Percentual baixo e prazo curto
                });
            })
            ->with(['etapa', 'recursos'])
            ->orderBy('data_prazo', 'asc')
            ->limit(10)
            ->get();

        // 5. Dados para preencher filtros na view
        $todosRecursos = Usuario::recurso()->ativo()->orderBy('nome')->get();
        $todasEtapas = Etapa::orderBy('ordem')->get();

        return view('dashboard.index', compact(
            'totalSolicitacoes', 'totalSaus', 'totalSds', 'emAndamento',
            'concluidas', 'atrasadas', 'percentualMedio', 'entregasProximas',
            'graficoEtapas', 'graficoTipos', 'graficoFaixas', 'graficoRecursos',
            'graficoMeses', 'graficoPrazosEntregas', 'painelAtencao',
            'todosRecursos', 'todasEtapas'
        ));
    }

    /**
     * Aplica filtros complexos na query base.
     */
    private function aplicarFiltros($query, Request $request)
    {
        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function($q) use ($busca) {
                $q->where('numero', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%")
                  ->orWhere('nome_solicitante', 'like', "%{$busca}%");
            });
        }

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

        if ($request->filled('faixa_conclusao')) {
            $faixa = $request->input('faixa_conclusao');
            if ($faixa === '0') {
                $query->where('percentual_conclusao', 0);
            } elseif ($faixa === '1-25') {
                $query->whereBetween('percentual_conclusao', [1, 25]);
            } elseif ($faixa === '26-50') {
                $query->whereBetween('percentual_conclusao', [26, 50]);
            } elseif ($faixa === '51-75') {
                $query->whereBetween('percentual_conclusao', [51, 75]);
            } elseif ($faixa === '76-99') {
                $query->whereBetween('percentual_conclusao', [76, 99]);
            } elseif ($faixa === '100') {
                $query->where('percentual_conclusao', 100);
            }
        }

        if ($request->filled('abertura_de')) {
            $query->whereDate('data_abertura', '>=', $request->input('abertura_de'));
        }
        if ($request->filled('abertura_ate')) {
            $query->whereDate('data_abertura', '<=', $request->input('abertura_ate'));
        }

        if ($request->filled('prazo_de')) {
            $query->whereDate('data_prazo', '>=', $request->input('prazo_de'));
        }
        if ($request->filled('prazo_ate')) {
            $query->whereDate('data_prazo', '<=', $request->input('prazo_ate'));
        }

        // Filtro rápido de situação
        if ($request->filled('situacao')) {
            $situacao = $request->input('situacao');
            if ($situacao === 'atrasadas') {
                $query->where('percentual_conclusao', '<', 100)
                      ->where('data_prazo', '<', now());
            } elseif ($situacao === 'concluidas') {
                $query->where('percentual_conclusao', 100);
            } elseif ($situacao === 'em_andamento') {
                $query->whereBetween('percentual_conclusao', [1, 99]);
            } elseif ($situacao === 'nao_iniciadas') {
                $query->where('percentual_conclusao', 0);
            }
        }

        return $query;
    }
}
