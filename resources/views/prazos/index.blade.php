@extends('layouts.app')

@section('title', 'Prazos e Cronograma - Gestão SAU & SD')

@section('breadcrumbs')
    <span class="breadcrumbs-item text-secondary">Prazos</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Prazos e Cronograma</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Gerencie prazos, vencimentos e confira metas de entrega.</p>
        </div>
    </div>

    <!-- CARDS DE METRICS DE VENCIMENTO -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-6 g-3 mb-4">
        <!-- Atrasadas -->
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['situacao' => 'atrasadas']) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Atrasadas</span>
                        <span class="h3 fw-bold mb-0 text-danger">{{ $cntAtrasadas }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Vencem Hoje -->
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['situacao' => 'hoje']) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Vencem Hoje</span>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $cntHoje }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Vencem Amanhã -->
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['situacao' => 'amanha']) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Vencem Amanhã</span>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $cntAmanha }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Nos Próximos 7 Dias -->
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['situacao' => 'proximos_7_dias']) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Próximos 7 dias</span>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $cnt7dias }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 22c5.52 0 10-4.48 10-10S17.52 2 12 2 2 6.48 2 12s4.48 10 10 10zm1-15h-2v6h6v-2h-4V7z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Concluídas no Prazo -->
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['situacao' => 'concluidas_no_prazo']) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">No Prazo</span>
                        <span class="h3 fw-bold mb-0 text-success">{{ $cntNoPrazo }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.42L9 19 21 7l-1.42-1.42z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Concluídas com Atraso -->
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['situacao' => 'concluidas_com_atraso']) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Com Atraso</span>
                        <span class="h3 fw-bold mb-0 text-danger">{{ $cntComAtraso }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- FILTROS DE PRAZOS -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <div class="card-body p-4">
            <form action="{{ route('prazos.index') }}" method="GET" class="row g-3 align-items-end">
                <!-- Tipo -->
                <div class="col-6 col-md-2">
                    <label for="tipo" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="SAU" {{ request('tipo') === 'SAU' ? 'selected' : '' }}>SAU</option>
                        <option value="SD" {{ request('tipo') === 'SD' ? 'selected' : '' }}>SD</option>
                    </select>
                </div>

                <!-- Etapa -->
                <div class="col-6 col-md-2">
                    <label for="etapa_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Etapa</label>
                    <select name="etapa_id" id="etapa_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($todasEtapas as $et)
                            <option value="{{ $et->id }}" {{ request('etapa_id') == $et->id ? 'selected' : '' }}>{{ $et->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Recurso -->
                <div class="col-12 col-md-3">
                    <label for="recurso_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Responsável</label>
                    <select name="recurso_id" id="recurso_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($todosRecursos as $rec)
                            <option value="{{ $rec->id }}" {{ request('recurso_id') == $rec->id ? 'selected' : '' }}>{{ $rec->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status de vencimento -->
                <div class="col-12 col-md-3">
                    <label for="situacao" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Filtro de Vencimento</label>
                    <select name="situacao" id="situacao" class="form-select form-select-sm">
                        <option value="">Todos os status</option>
                        <option value="atrasadas" {{ request('situacao') === 'atrasadas' ? 'selected' : '' }}>Atrasadas</option>
                        <option value="hoje" {{ request('situacao') === 'hoje' ? 'selected' : '' }}>Vencem Hoje</option>
                        <option value="amanha" {{ request('situacao') === 'amanha' ? 'selected' : '' }}>Vencem Amanhã</option>
                        <option value="proximos_7_dias" {{ request('situacao') === 'proximos_7_dias' ? 'selected' : '' }}>Próximos 7 dias</option>
                        <option value="concluidas_no_prazo" {{ request('situacao') === 'concluidas_no_prazo' ? 'selected' : '' }}>Concluídas no prazo</option>
                        <option value="concluidas_com_atraso" {{ request('situacao') === 'concluidas_com_atraso' ? 'selected' : '' }}>Concluídas com atraso</option>
                    </select>
                </div>

                <!-- Data Prazo (De) -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label for="periodo_de" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Prazo De</label>
                    <input type="date" name="periodo_de" id="periodo_de" class="form-control form-control-sm" value="{{ request('periodo_de') }}">
                </div>

                <!-- Data Prazo (Até) -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label for="periodo_ate" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Prazo Até</label>
                    <input type="date" name="periodo_ate" id="periodo_ate" class="form-control form-control-sm" value="{{ request('periodo_ate') }}">
                </div>

                <!-- Botões -->
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-filtrar-prazos">Filtrar</button>
                    <a href="{{ route('prazos.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- LISTA DE PRAZOS -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark" style="background-color: var(--navy);">
                    <tr>
                        <th>Identificação</th>
                        <th>Tipo</th>
                        <th>Solicitante</th>
                        <th>Descrição Resumida</th>
                        <th>Etapa</th>
                        <th>Progresso</th>
                        <th>Responsáveis</th>
                        <th>Prazo Estimado</th>
                        <th>Data Real de Entrega</th>
                        <th>Tempo Restante / Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitacoes as $sol)
                        <tr class="{{ $sol->is_atrasada ? 'table-danger-subtle' : '' }}">
                            <!-- Identificação -->
                            <td class="fw-bold text-dark">{{ $sol->identificacao }}</td>
                            
                            <!-- Tipo -->
                            <td>
                                <span class="badge {{ $sol->tipo === 'SAU' ? 'bg-primary' : 'bg-warning text-dark' }}">{{ $sol->tipo }}</span>
                            </td>
                            
                            <!-- Solicitante -->
                            <td>{{ $sol->nome_solicitante }}</td>

                            <!-- Descrição -->
                            <td style="max-width: 200px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $sol->descricao }}">
                                {{ $sol->descricao }}
                            </td>
                            
                            <!-- Etapa -->
                            <td>
                                <x-badge :status="$sol->etapa->nome" />
                            </td>

                            <!-- Progresso -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 55px; height: 6px; background-color: #e9ecef;">
                                        <div class="progress-bar" style="width: {{ $sol->percentual_conclusao }}%; background-color: var(--blue-primary);"></div>
                                    </div>
                                    <span class="fw-bold" style="font-size: 0.8rem;">{{ $sol->percentual_conclusao }}%</span>
                                </div>
                            </td>

                            <!-- Responsáveis -->
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($sol->recursos as $rec)
                                        @php $inc = mb_substr($rec->nome, 0, 1); @endphp
                                        <span class="badge bg-secondary text-white py-1 px-2" title="{{ $rec->nome }}">
                                            {{ explode(' ', $rec->nome)[0] }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Prazo -->
                            <td>
                                @if($sol->data_prazo)
                                    {{ $sol->data_prazo->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-secondary" style="font-style: italic;">Não aplicável (SAU)</span>
                                @endif
                            </td>

                            <!-- Entrega Real -->
                            <td>
                                @if($sol->data_entrega)
                                    {{ $sol->data_entrega->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-secondary italic" style="font-style: italic;">Não entregue</span>
                                @endif
                            </td>

                            <!-- Tempo Restante e Badge de Situação -->
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <x-badge :status="$sol->situacao_calculada" />
                                    <span class="fw-semibold text-secondary" style="font-size: 0.75rem;">
                                        {{ $sol->tempo_restante_texto }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-secondary">
                                Nenhuma solicitação correspondente ao filtro de prazos no momento.
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
@endsection
