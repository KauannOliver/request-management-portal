@extends('layouts.app')

@section('title', 'Solicitações - Gestão SAU & SD')

@section('breadcrumbs')
    <span class="breadcrumbs-item text-secondary">Solicitações</span>
@endsection

@section('content')
    <!-- CABEÇALHO DA LISTAGEM -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Solicitações (SAU & SD)</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Lista geral e acompanhamento de tarefas, etapas e percentuais.</p>
        </div>

        <div class="d-flex gap-2">
            @if(Auth::user()->isRecurso())
                <a href="{{ route('solicitacoes.lixeira') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                    <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Ver Lixeira
                </a>
                <a href="{{ route('solicitacoes.create') }}" class="btn btn-primary d-flex align-items-center gap-1" style="background-color: var(--blue-primary); border-color: var(--blue-primary);">
                    <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Cadastrar Solicitação
                </a>
            @endif
        </div>
    </div>

    <!-- FILTROS AVANÇADOS -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <div class="card-body p-4">
            <form action="{{ route('solicitacoes.index') }}" method="GET" class="row g-3 align-items-end">
                <!-- Busca livre -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label for="busca" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Buscar Solicitação</label>
                    <input type="text" name="busca" id="busca" class="form-control form-control-sm" placeholder="Número, descrição ou solicitante..." value="{{ request('busca') }}">
                </div>

                <!-- Tipo -->
                <div class="col-6 col-md-2 col-lg-1">
                    <label for="tipo" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="SAU" {{ request('tipo') === 'SAU' ? 'selected' : '' }}>SAU</option>
                        <option value="SD" {{ request('tipo') === 'SD' ? 'selected' : '' }}>SD</option>
                    </select>
                </div>

                <!-- Etapa -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label for="etapa_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Etapa</label>
                    <select name="etapa_id" id="etapa_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($todasEtapas as $et)
                            <option value="{{ $et->id }}" {{ request('etapa_id') == $et->id ? 'selected' : '' }}>{{ $et->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Responsável -->
                <div class="col-12 col-md-3 col-lg-2">
                    <label for="recurso_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Responsável</label>
                    <select name="recurso_id" id="recurso_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($todosRecursos as $rec)
                            <option value="{{ $rec->id }}" {{ request('recurso_id') == $rec->id ? 'selected' : '' }}>{{ $rec->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Faixa de Conclusão -->
                <div class="col-6 col-md-3 col-lg-1">
                    <label for="faixa_conclusao" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Progresso</label>
                    <select name="faixa_conclusao" id="faixa_conclusao" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="0" {{ request('faixa_conclusao') === '0' ? 'selected' : '' }}>0% (Não Iniciado)</option>
                        <option value="1-25" {{ request('faixa_conclusao') === '1-25' ? 'selected' : '' }}>1% a 25%</option>
                        <option value="26-50" {{ request('faixa_conclusao') === '26-50' ? 'selected' : '' }}>26% a 50%</option>
                        <option value="51-75" {{ request('faixa_conclusao') === '51-75' ? 'selected' : '' }}>51% a 75%</option>
                        <option value="76-99" {{ request('faixa_conclusao') === '76-99' ? 'selected' : '' }}>76% a 99%</option>
                        <option value="100" {{ request('faixa_conclusao') === '100' ? 'selected' : '' }}>100% (Concluído)</option>
                    </select>
                </div>

                <!-- Situação de Atraso -->
                <div class="col-6 col-md-3 col-lg-1">
                    <label for="atrasadas" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Atraso</label>
                    <select name="atrasadas" id="atrasadas" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="1" {{ request('atrasadas') == '1' ? 'selected' : '' }}>Somente Atrasadas</option>
                    </select>
                </div>

                <!-- Botões -->
                <div class="col-12 col-md-4 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill text-nowrap" style="background-color: var(--blue-primary); border-color: var(--blue-primary);">Filtrar</button>
                    <a href="{{ route('solicitacoes.index') }}" class="btn btn-outline-secondary btn-sm flex-fill text-nowrap">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABELA DE SOLICITAÇÕES -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark" style="background-color: var(--navy);">
                    <tr>
                        @if(Auth::user()->isRecurso())
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="bulk-select-all" class="form-check-input" style="cursor: pointer;">
                            </th>
                        @endif
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'numero', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Identificação
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'numero')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'tipo', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Tipo
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'tipo')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Solicitante</th>
                        <th>Descrição Resumida</th>
                        <th>Responsáveis</th>
                        <th>Etapa</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'percentual_conclusao', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Progresso
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'percentual_conclusao')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'data_abertura', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Abertura
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'data_abertura')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'data_prazo', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Prazo Est.
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'data_prazo')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Situação</th>
                        <th style="text-align: center; width: 180px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitacoes as $sol)
                        <tr class="{{ $sol->is_atrasada ? 'table-danger-subtle' : '' }}">
                            @if(Auth::user()->isRecurso())
                                <td style="text-align: center;">
                                    <input type="checkbox" name="selected_solicitacoes[]" value="{{ $sol->id }}" class="bulk-checkbox form-check-input" style="cursor: pointer;">
                                </td>
                            @endif
                            <!-- Identificação -->
                            <td class="fw-bold text-dark">{{ $sol->identificacao }}</td>
                            
                            <!-- Tipo -->
                            <td>
                                <span class="badge {{ $sol->tipo === 'SAU' ? 'bg-primary' : 'bg-warning text-dark' }}">{{ $sol->tipo }}</span>
                            </td>
                            
                            <!-- Solicitante -->
                            <td>{{ $sol->nome_solicitante }}</td>

                            <!-- Descrição Resumida -->
                            <td style="max-width: 180px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $sol->descricao }}">
                                {{ $sol->descricao }}
                            </td>
                            
                            <!-- Recursos Responsáveis (Chips) -->
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($sol->recursos as $rec)
                                        @php $inc = mb_substr($rec->nome, 0, 1); @endphp
                                        <span class="badge bg-secondary d-inline-flex align-items-center gap-1 text-white py-1 px-2" title="{{ $rec->nome }} ({{ $rec->email }})">
                                            <span class="d-flex align-items-center justify-content-center bg-white text-dark rounded-circle" style="width: 14px; height: 14px; font-size: 0.65rem; font-weight: 700;">{{ $inc }}</span>
                                            {{ explode(' ', $rec->nome)[0] }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Etapa -->
                            <td>
                                <x-badge :status="$sol->etapa->nome" />
                            </td>

                            <!-- Progresso (percentual) -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 50px; height: 6px; background-color: #e9ecef;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $sol->percentual_conclusao }}%; background-color: var(--blue-primary);" aria-valuenow="{{ $sol->percentual_conclusao }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="fw-semibold" style="font-size: 0.8rem;">{{ $sol->percentual_conclusao }}%</span>
                                </div>
                            </td>

                            <!-- Data Abertura -->
                            <td>{{ $sol->data_abertura->format('d/m/Y H:i') }}</td>

                            <!-- Data Prazo -->
                            <td>
                                @if($sol->data_prazo)
                                    {{ $sol->data_prazo->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-secondary" style="font-style: italic;">Não aplicável (SAU)</span>
                                @endif
                            </td>

                            <!-- Situação Calculada -->
                            <td>
                                <x-badge :status="$sol->situacao_calculada" />
                            </td>

                            <!-- Ações baseadas em perfil -->
                            <td style="text-align: center;">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('solicitacoes.show', $sol->id) }}" class="btn btn-light btn-sm fw-semibold border" id="btn-sol-ver-{{ $sol->id }}" title="Visualizar">
                                        Detalhar
                                    </a>

                                    @if(Auth::user()->isRecurso())
                                        <a href="{{ route('solicitacoes.edit', $sol->id) }}" class="btn btn-outline-primary btn-sm fw-semibold" id="btn-sol-editar-{{ $sol->id }}" title="Editar">
                                            Editar
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="btn-sol-deletar-{{ $sol->id }}" onclick="abrirModalExcluir({{ $sol->id }}, '{{ $sol->identificacao }}')" title="Excluir">
                                            Excluir
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-secondary">
                                Nenhuma solicitação encontrada correspondente aos filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINAÇÃO E SELETOR DE PER_PAGE -->
        <div class="card-footer bg-white p-3 border-0">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    {{ $solicitacoes->links() }}
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary" style="font-size: 0.85rem;">Itens por página:</span>
                    <select class="form-select form-select-sm" style="width: auto; cursor: pointer;" onchange="window.location.href = this.value;">
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 40]) }}" {{ request('per_page') == 40 ? 'selected' : '' }}>40</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- BARRA DE AÇÃO EM LOTE FLUTUANTE (Exclusivo Recurso) -->
    @if(Auth::user()->isRecurso())
        <div id="bulk-action-bar" class="d-none position-fixed bottom-0 start-50 translate-middle-x mb-4 bg-dark text-white p-3 rounded shadow-lg align-items-center gap-3" style="z-index: 1050; border: 1px solid rgba(255,255,255,0.15);">
            <span id="bulk-selected-count" class="fw-bold">0 itens selecionados</span>
            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalBulkReassign()" style="background-color: var(--blue-primary); border-color: var(--blue-primary);">
                Reatribuir Recursos
            </button>
            <button type="button" class="btn btn-outline-light btn-sm" onclick="limparSelecao()">
                Cancelar
            </button>
        </div>

        <!-- MODAL DE REATRIBUIÇÃO EM LOTE -->
        <div id="modal-bulk-reassign" class="modal-overlay">
            <div class="modal-card" style="max-width: 480px;">
                <div class="modal-header">
                    <h3 class="modal-title">Reatribuir Recursos em Lote</h3>
                    <button type="button" class="modal-close-btn" onclick="fecharModalBulkReassign()" aria-label="Fechar">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                    </button>
                </div>
                <form action="{{ route('solicitacoes.bulk-reassign') }}" method="POST" id="form-bulk-reassign" onsubmit="return validarEnvioLote();">
                    @csrf
                    <div class="modal-body">
                        <p class="text-secondary mb-3" style="font-size: 0.9rem;">
                            Os recursos selecionados abaixo substituirão todos os responsáveis anteriores das solicitações marcadas.
                        </p>
                        
                        <!-- Inputs ocultos gerados por JS contendo os IDs das solicitações selecionadas -->
                        <div id="bulk-hidden-inputs"></div>

                        <div class="form-group w-100">
                            <label class="form-label fw-semibold mb-2">Selecione os Recursos Responsáveis <span class="text-danger">*</span></label>
                            <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                @foreach($todosRecursos as $rec)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="recursos_ids[]" value="{{ $rec->id }}" id="bulk-rec-{{ $rec->id }}">
                                        <label class="form-check-label text-dark" style="cursor: pointer;" for="bulk-rec-{{ $rec->id }}">
                                            {{ $rec->nome }} ({{ $rec->email }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="fecharModalBulkReassign()">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background-color: var(--blue-primary); border-color: var(--blue-primary);">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SCRIPTS KANBAN BULK ACTION -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('bulk-select-all');
                const checkBoxes = document.querySelectorAll('.bulk-checkbox');
                const bulkBar = document.getElementById('bulk-action-bar');
                const selectedCount = document.getElementById('bulk-selected-count');

                function updateBulkBar() {
                    const checked = document.querySelectorAll('.bulk-checkbox:checked');
                    if (checked.length > 0) {
                        bulkBar.classList.remove('d-none');
                        bulkBar.classList.add('d-flex');
                        selectedCount.textContent = checked.length + ' item(ns) selecionado(s)';
                    } else {
                        bulkBar.classList.add('d-none');
                        bulkBar.classList.remove('d-flex');
                    }
                }

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        checkBoxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
                        updateBulkBar();
                    });
                }

                checkBoxes.forEach(cb => {
                    cb.addEventListener('change', function() {
                        if (!cb.checked) {
                            selectAllCheckbox.checked = false;
                        } else {
                            const allChecked = document.querySelectorAll('.bulk-checkbox:checked').length === checkBoxes.length;
                            selectAllCheckbox.checked = allChecked;
                        }
                        updateBulkBar();
                    });
                });
            });

            function limparSelecao() {
                const selectAll = document.getElementById('bulk-select-all');
                if (selectAll) selectAll.checked = false;
                document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = false);
                const bulkBar = document.getElementById('bulk-action-bar');
                if (bulkBar) {
                    bulkBar.classList.add('d-none');
                    bulkBar.classList.remove('d-flex');
                }
            }

            function abrirModalBulkReassign() {
                const checked = document.querySelectorAll('.bulk-checkbox:checked');
                const container = document.getElementById('bulk-hidden-inputs');
                container.innerHTML = '';
                
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'solicitacoes_ids[]';
                    input.value = cb.value;
                    container.appendChild(input);
                });

                document.getElementById('modal-bulk-reassign').classList.add('active');
            }

            function fecharModalBulkReassign() {
                document.getElementById('modal-bulk-reassign').classList.remove('active');
            }

            function validarEnvioLote() {
                const checkedRecursos = document.querySelectorAll('input[name="recursos_ids[]"]:checked');
                if (checkedRecursos.length === 0) {
                    alert('Por favor, selecione pelo menos um recurso responsável.');
                    return false;
                }
                return true;
            }
        </script>
    @endif
@endsection
