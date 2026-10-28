<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitacao;
use App\Models\Etapa;
use App\Models\Usuario;
use App\Models\LogAuditoria;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreSolicitacaoRequest;
use App\Http\Requests\UpdateSolicitacaoRequest;
use App\Actions\Solicitacao\CreateSolicitacaoAction;
use App\Actions\Solicitacao\UpdateSolicitacaoAction;
use App\Actions\Solicitacao\DeleteSolicitacaoAction;
use App\Actions\Solicitacao\RestoreSolicitacaoAction;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SolicitacaoController extends Controller
{
    /**
     * Exibe a listagem das solicitações (não excluídas).
     */
    public function index(Request $request)
    {
        $query = Solicitacao::with(['etapa', 'recursos']);

        // Aplicar ordenação e filtros
        $query = $this->aplicarFiltrosEOrdenacao($query, $request);

        // Paginação dinâmica (10, 25 ou 40 itens)
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 40])) {
            $perPage = 10;
        }
        $solicitacoes = $query->paginate($perPage)->withQueryString();

        $todasEtapas = Etapa::orderBy('ordem')->get();
        $todosRecursos = Usuario::recurso()->ativo()->orderBy('nome')->get();

        return view('solicitacoes.index', compact('solicitacoes', 'todasEtapas', 'todosRecursos'));
    }

    /**
     * Exibe o formulário de cadastro.
     */
    public function create()
    {
        Gate::authorize('create', Solicitacao::class);

        $etapas = Etapa::orderBy('ordem')->get();
        $recursos = Usuario::recurso()->ativo()->orderBy('nome')->get();

        return view('solicitacoes.create', compact('etapas', 'recursos'));
    }

    /**
     * Armazena uma nova solicitação.
     */
    public function store(StoreSolicitacaoRequest $request, CreateSolicitacaoAction $action)
    {
        Gate::authorize('create', Solicitacao::class);

        // Validar data de prazo
        $dataAbertura = Carbon::parse($request->data_abertura);
        $dataPrazo = Carbon::parse($request->data_prazo);
        if ($dataPrazo->lt($dataAbertura)) {
            return back()->withErrors(['data_prazo' => 'A data de prazo não pode ser anterior à data de abertura.'])->withInput();
        }

        // Validação de unicidade composta tipo + numero
        $existeDuplicado = Solicitacao::where('tipo', $request->tipo)
            ->where('numero', $request->numero)
            ->exists();

        if ($existeDuplicado) {
            return back()->withErrors([
                'numero' => "Já existe uma solicitação do tipo {$request->tipo} com o número {$request->numero}."
            ])->withInput();
        }

        $action->execute($request->validated());

        return redirect()->route('solicitacoes.index')->with('success', 'Solicitação cadastrada com sucesso!');
    }

    /**
     * Exibe os detalhes da solicitação e sua linha do tempo.
     */
    public function show($id)
    {
        $solicitacao = Solicitacao::withTrashed()->with(['etapa', 'recursos', 'criadoPor', 'atualizadoPor', 'excluidoPor'])->findOrFail($id);
        
        Gate::authorize('view', $solicitacao);

        // Montar a linha do tempo com base nos logs de auditoria
        // Gestor visualiza apenas eventos funcionais e sem detalhes técnicos
        $acoesFuncionais = [
            'Cadastro de solicitação',
            'Mudança de etapa',
            'Mudança de percentual',
            'Mudança de prazo',
            'Mudança de data de entrega',
            'Exclusão lógica',
            'Restauração'
        ];

        $logsQuery = LogAuditoria::where('entidade', 'solicitacoes')
            ->where('entidade_id', $solicitacao->id)
            ->orderBy('created_at', 'desc');

        if (Auth::user()->isGestor()) {
            $logsQuery->whereIn('acao', $acoesFuncionais);
        }

        $logsLinhaTempo = $logsQuery->get();

        return view('solicitacoes.show', compact('solicitacao', 'logsLinhaTempo'));
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit($id)
    {
        $solicitacao = Solicitacao::findOrFail($id);
        
        Gate::authorize('update', $solicitacao);

        $etapas = Etapa::orderBy('ordem')->get();
        $recursos = Usuario::recurso()->ativo()->orderBy('nome')->get();
        $recursosAssociados = $solicitacao->recursos->pluck('id')->toArray();

        return view('solicitacoes.edit', compact('solicitacao', 'etapas', 'recursos', 'recursosAssociados'));
    }

    /**
     * Atualiza a solicitação.
     */
    public function update(UpdateSolicitacaoRequest $request, $id, UpdateSolicitacaoAction $action)
    {
        $solicitacao = Solicitacao::findOrFail($id);
        
        Gate::authorize('update', $solicitacao);

        // Validar data de prazo
        $dataAbertura = Carbon::parse($request->data_abertura);
        $dataPrazo = Carbon::parse($request->data_prazo);
        if ($dataPrazo->lt($dataAbertura)) {
            return back()->withErrors(['data_prazo' => 'A data de prazo não pode ser anterior à data de abertura.'])->withInput();
        }

        // Validação de unicidade composta tipo + numero (excluindo a si própria)
        $existeDuplicado = Solicitacao::where('tipo', $request->tipo)
            ->where('numero', $request->numero)
            ->where('id', '!=', $solicitacao->id)
            ->exists();

        if ($existeDuplicado) {
            return back()->withErrors([
                'numero' => "Já existe uma solicitação do tipo {$request->tipo} com o número {$request->numero}."
            ])->withInput();
        }

        // Caso a data de entrega tenha sido enviada e o percentual não seja 100%
        if ($request->filled('data_entrega') && (int)$request->percentual_conclusao < 100) {
            return back()->withErrors(['data_entrega' => 'Só é permitido preencher data de entrega para solicitações com 100% de conclusão.'])->withInput();
        }

        // Validar se data de entrega não é anterior à abertura
        if ($request->filled('data_entrega')) {
            $dataEntrega = Carbon::parse($request->data_entrega);
            if ($dataEntrega->lt($dataAbertura)) {
                return back()->withErrors(['data_entrega' => 'A data de entrega não pode ser anterior à data de abertura.'])->withInput();
            }
        }

        $action->execute($solicitacao, $request->validated());

        return redirect()->route('solicitacoes.show', $solicitacao->id)->with('success', 'Solicitação atualizada com sucesso!');
    }

    /**
     * Realiza a exclusão lógica da solicitação (oculta de consultas padrão).
     */
    public function destroy(Request $request, $id, DeleteSolicitacaoAction $action)
    {
        $solicitacao = Solicitacao::findOrFail($id);
        
        Gate::authorize('delete', $solicitacao);

        $request->validate([
            'motivo_exclusao' => 'required|string|min:5|max:500'
        ]);

        $action->execute($solicitacao, $request->input('motivo_exclusao'));

        return redirect()->route('solicitacoes.index')->with('success', "A solicitação {$solicitacao->identificacao} foi excluída logicamente.");
    }

    /**
     * Exibe a área de solicitações excluídas logicamente.
     */
    public function lixeira(Request $request)
    {
        Gate::authorize('create', Solicitacao::class); // Apenas Recurso tem acesso a essa área administrativa

        $query = Solicitacao::onlyTrashed()->with(['etapa', 'excluidoPor']);

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function($q) use ($busca) {
                $q->where('numero', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%")
                  ->orWhere('nome_solicitante', 'like', "%{$busca}%");
            });
        }

        // Ordenação
        $sort = $request->input('sort', 'deleted_at');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = ['numero', 'tipo', 'nome_solicitante', 'deleted_at'];
        if (in_array($sort, $allowedSorts)) {
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'desc';
            }
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        // Paginação dinâmica (10, 25 ou 40 itens)
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 40])) {
            $perPage = 10;
        }
        $solicitacoesExcluidas = $query->paginate($perPage)->withQueryString();

        return view('solicitacoes.lixeira', compact('solicitacoesExcluidas'));
    }

    /**
     * Restaura uma solicitação excluída logicamente.
     */
    public function restore($id, RestoreSolicitacaoAction $action)
    {
        $solicitacao = Solicitacao::onlyTrashed()->findOrFail($id);
        
        Gate::authorize('restore', $solicitacao);

        $action->execute($solicitacao);

        return redirect()->route('solicitacoes.lixeira')->with('success', "A solicitação {$solicitacao->identificacao} foi restaurada com sucesso.");
    }

    /**
     * Aplica filtros e ordenação na listagem.
     */
    private function aplicarFiltrosEOrdenacao($query, Request $request)
    {
        // Filtro de Busca livre
        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function($q) use ($busca) {
                $q->where('numero', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%")
                  ->orWhere('nome_solicitante', 'like', "%{$busca}%");
            });
        }

        // Filtro de tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        // Filtro de etapa
        if ($request->filled('etapa_id')) {
            $query->where('etapa_id', $request->input('etapa_id'));
        }

        // Filtro por recurso responsável
        if ($request->filled('recurso_id')) {
            $query->whereHas('recursos', function($q) use ($request) {
                $q->where('usuarios.id', $request->input('recurso_id'));
            });
        }

        // Filtro de faixa de percentual
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

        // Filtro de atrasadas
        if ($request->filled('atrasadas') && $request->input('atrasadas') == 1) {
            $query->where('percentual_conclusao', '<', 100)
                  ->where('data_prazo', '<', now());
        }

        // Filtro de período por data de abertura ou prazo
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

        // Ordenação
        $ordenarPor = $request->input('sort', 'created_at');
        $direcao = $request->input('direction', 'desc');

        // Garantir campos válidos para ordenação
        $camposValidos = ['numero', 'tipo', 'percentual_conclusao', 'data_abertura', 'data_prazo', 'data_entrega', 'created_at'];
        if (in_array($ordenarPor, $camposValidos)) {
            $query->orderBy($ordenarPor, $direcao);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Reatribui em lote os responsáveis por várias solicitações selecionadas.
     */
    public function bulkReassign(Request $request)
    {
        Gate::authorize('create', Solicitacao::class); // Apenas recursos administradores

        $request->validate([
            'solicitacoes_ids' => 'required|array|min:1',
            'solicitacoes_ids.*' => 'exists:solicitacoes,id',
            'recursos_ids' => 'required|array|min:1',
            'recursos_ids.*' => 'exists:usuarios,id'
        ], [
            'solicitacoes_ids.required' => 'Nenhuma solicitação foi selecionada.',
            'recursos_ids.required' => 'Selecione pelo menos um recurso responsável.'
        ]);

        $solIds = $request->input('solicitacoes_ids');
        $recIds = $request->input('recursos_ids');

        DB::transaction(function () use ($solIds, $recIds) {
            foreach ($solIds as $solId) {
                $solicitacao = Solicitacao::findOrFail($solId);
                
                $recursosAtuais = $solicitacao->recursos->pluck('id')->toArray();
                $incluidos = array_diff($recIds, $recursosAtuais);
                $removidos = array_diff($recursosAtuais, $recIds);

                // Sincronizar na tabela pivot
                $solicitacao->recursos()->sync(array_fill_keys($recIds, [
                    'atribuido_por_id' => Auth::id(),
                    'atribuido_em' => now()
                ]));

                // Registrar logs de inclusões
                foreach ($incluidos as $userId) {
                    $user = Usuario::find($userId);
                    AuditoriaService::registrar(
                        'Inclusão de recurso',
                        'solicitacoes',
                        $solicitacao->id,
                        "O recurso {$user->nome} foi atribuído em lote como responsável pela solicitação {$solicitacao->identificacao}.",
                        null,
                        ['usuario_id' => $user->id, 'nome' => $user->nome]
                    );
                }

                // Registrar logs de remoções
                foreach ($removidos as $userId) {
                    $user = Usuario::find($userId);
                    AuditoriaService::registrar(
                        'Remoção de recurso',
                        'solicitacoes',
                        $solicitacao->id,
                        "O recurso {$user->nome} foi removido em lote da responsabilidade pela solicitação {$solicitacao->identificacao}.",
                        ['usuario_id' => $user->id, 'nome' => $user->nome],
                        null
                    );
                }
            }
        });

        return back()->with('success', count($solIds) . ' solicitações foram reatribuídas com sucesso!');
    }
}
