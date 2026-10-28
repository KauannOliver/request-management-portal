@extends('layouts.app')

@section('title', 'Dashboard Executivo - Gestão SAU & SD')

@section('breadcrumbs')
    <span class="breadcrumbs-item text-secondary">Dashboard</span>
@endsection

@section('content')
    <!-- TÍTULO DA PÁGINA E AÇÕES RÁPIDAS -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Dashboard Executivo</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Estatísticas gerais de solicitações, prazos e fluxo de entregas em tempo real.</p>
        </div>
    </div>

    <!-- BARRA DE FILTROS GLOBAIS -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <div class="card-body p-4">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-3 align-items-end">
                <!-- Busca Livre -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label for="busca" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Buscar Termo</label>
                    <input type="text" name="busca" id="busca" class="form-control form-control-sm" placeholder="Número, descrição ou solicitante..." value="{{ request('busca') }}">
                </div>

                <!-- Filtro de Tipo -->
                <div class="col-6 col-md-2">
                    <label for="tipo" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="SAU" {{ request('tipo') === 'SAU' ? 'selected' : '' }}>SAU</option>
                        <option value="SD" {{ request('tipo') === 'SD' ? 'selected' : '' }}>SD</option>
                    </select>
                </div>

                <!-- Filtro de Etapa -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label for="etapa_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Etapa Atual</label>
                    <select name="etapa_id" id="etapa_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($todasEtapas as $et)
                            <option value="{{ $et->id }}" {{ request('etapa_id') == $et->id ? 'selected' : '' }}>{{ $et->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro de Recurso -->
                <div class="col-12 col-md-3 col-lg-2">
                    <label for="recurso_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Responsável</label>
                    <select name="recurso_id" id="recurso_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($todosRecursos as $rec)
                            <option value="{{ $rec->id }}" {{ request('recurso_id') == $rec->id ? 'selected' : '' }}>{{ $rec->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro de Faixa de Conclusão -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label for="faixa_conclusao" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Progresso</label>
                    <select name="faixa_conclusao" id="faixa_conclusao" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="0" {{ request('faixa_conclusao') === '0' ? 'selected' : '' }}>0% (Planejado)</option>
                        <option value="1-25" {{ request('faixa_conclusao') === '1-25' ? 'selected' : '' }}>1% a 25%</option>
                        <option value="26-50" {{ request('faixa_conclusao') === '26-50' ? 'selected' : '' }}>26% a 50%</option>
                        <option value="51-75" {{ request('faixa_conclusao') === '51-75' ? 'selected' : '' }}>51% a 75%</option>
                        <option value="76-99" {{ request('faixa_conclusao') === '76-99' ? 'selected' : '' }}>76% a 99%</option>
                        <option value="100" {{ request('faixa_conclusao') === '100' ? 'selected' : '' }}>100% (Concluído)</option>
                    </select>
                </div>

                <!-- Botões de Ação do Filtro -->
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-filtrar-dashboard">Filtrar</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm flex-fill">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- CARDS DE ESTATÍSTICA (INDICADORES) -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mb-4">
        <!-- Card 1: Total -->
        <div class="col">
            <a href="{{ route('solicitacoes.index', request()->query()) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Total de Solicitações</span>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $totalSolicitacoes }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-dark-subtle text-dark rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 2: SAUs -->
        <div class="col">
            <a href="{{ route('solicitacoes.index', array_merge(request()->query(), ['tipo' => 'SAU'])) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Solicitações SAU</span>
                        <span class="h3 fw-bold mb-0 text-primary">{{ $totalSaus }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 3: SDs -->
        <div class="col">
            <a href="{{ route('solicitacoes.index', array_merge(request()->query(), ['tipo' => 'SD'])) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Solicitações SD</span>
                        <span class="h3 fw-bold mb-0 text-warning">{{ $totalSds }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 4: Concluídas -->
        <div class="col">
            <a href="{{ route('solicitacoes.index', array_merge(request()->query(), ['faixa_conclusao' => '100'])) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Concluídas</span>
                        <span class="h3 fw-bold mb-0 text-success">{{ $concluidas }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.42L9 19 21 7l-1.42-1.42z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 5: Em Andamento -->
        <div class="col">
            <a href="{{ route('solicitacoes.index', array_merge(request()->query(), ['situacao' => 'em_andamento'])) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Em Andamento</span>
                        <span class="h3 fw-bold mb-0 text-info">{{ $emAndamento }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 6: Atrasadas -->
        <div class="col">
            <a href="{{ route('solicitacoes.index', array_merge(request()->query(), ['atrasadas' => '1'])) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Atrasadas</span>
                        <span class="h3 fw-bold mb-0 text-danger">{{ $atrasadas }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 7: Conclusão Média -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Progresso Médio</span>
                        <span class="h3 fw-bold mb-0 text-success">{{ number_format($percentualMedio, 1, ',', '.') }}%</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 8: Prazos nos Próximos 7 Dias -->
        <div class="col">
            <a href="{{ route('prazos.index', ['situacao' => 'proximos_7_dias']) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover" style="border-radius: var(--radius-md);">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Prazos 7 Dias</span>
                        <span class="h3 fw-bold mb-0 text-warning">{{ $entregasProximas }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 40px; height: 40px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M20 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- GRID DE GRÁFICOS (CHART.JS) -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mb-4">
        <!-- Gráfico 1: Distribuição por Etapa -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
                <div class="card-header border-0 bg-transparent pt-3 px-3 pb-0">
                    <h4 class="h6 fw-bold text-dark mb-0">Distribuição por Etapa</h4>
                </div>
                <div class="card-body p-3" style="height: 250px;">
                    <canvas id="chartEtapas"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Solicitações por Tipo -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
                <div class="card-header border-0 bg-transparent pt-3 px-3 pb-0">
                    <h4 class="h6 fw-bold text-dark mb-0">Proporção por Tipo (SAU vs SD)</h4>
                </div>
                <div class="card-body p-3" style="height: 250px;">
                    <canvas id="chartTipos"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 3: Distribuição por Faixa de Conclusão -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
                <div class="card-header border-0 bg-transparent pt-3 px-3 pb-0">
                    <h4 class="h6 fw-bold text-dark mb-0">Progresso por Faixa</h4>
                </div>
                <div class="card-body p-3" style="height: 250px;">
                    <canvas id="chartFaixas"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 4: Quantidade por Recurso -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
                <div class="card-header border-0 bg-transparent pt-3 px-3 pb-0">
                    <h4 class="h6 fw-bold text-dark mb-0">Demandas por Responsável (Top 10)</h4>
                </div>
                <div class="card-body p-3" style="height: 250px;">
                    <canvas id="chartRecursos"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 5: Evolução Mensal -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
                <div class="card-header border-0 bg-transparent pt-3 px-3 pb-0">
                    <h4 class="h6 fw-bold text-dark mb-0">Evolução Mensal (Abertas vs Concluídas)</h4>
                </div>
                <div class="card-body p-3" style="height: 250px;">
                    <canvas id="chartMensal"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 6: Cumprimento de Prazos -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
                <div class="card-header border-0 bg-transparent pt-3 px-3 pb-0">
                    <h4 class="h6 fw-bold text-dark mb-0">Cumprimento de Prazos (Concluídas)</h4>
                </div>
                <div class="card-body p-3" style="height: 250px;">
                    <canvas id="chartPrazos"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- PAINEL DE ATENÇÃO: SOLICITAÇÕES QUE EXIGEM ATENÇÃO -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h3 class="h5 fw-bold text-dark mb-1">Solicitações que Exigem Atenção Crítica</h3>
            <span class="badge bg-danger-subtle text-danger px-3 py-2 fw-bold" style="font-size: 0.85rem;">
                Prioridades de Atraso ou Prazos Próximos
            </span>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-dark" style="background-color: var(--navy);">
                        <tr>
                            <th>Identificação</th>
                            <th>Tipo</th>
                            <th>Solicitante</th>
                            <th>Descrição</th>
                            <th>Etapa</th>
                            <th>Progresso</th>
                            <th>Prazo</th>
                            <th>Alerta</th>
                            <th style="text-align: center; width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($painelAtencao as $sol)
                            <tr class="{{ $sol->is_atrasada ? 'table-danger-subtle' : '' }}">
                                <td class="fw-bold text-dark">{{ $sol->identificacao }}</td>
                                <td>
                                    <span class="badge {{ $sol->tipo === 'SAU' ? 'bg-primary' : 'bg-warning text-dark' }}">{{ $sol->tipo }}</span>
                                </td>
                                <td>{{ $sol->nome_solicitante }}</td>
                                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $sol->descricao }}">
                                    {{ $sol->descricao }}
                                </td>
                                <td>
                                    <x-badge :status="$sol->etapa->nome" />
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress" style="width: 55px; height: 6px; background-color: #e9ecef;">
                                            <div class="progress-bar" style="width: {{ $sol->percentual_conclusao }}%; background-color: var(--blue-primary);"></div>
                                        </div>
                                        <span class="fw-bold" style="font-size: 0.8rem;">{{ $sol->percentual_conclusao }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($sol->data_prazo)
                                        {{ $sol->data_prazo->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-secondary" style="font-style: italic;">Não aplicável (SAU)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sol->is_atrasada)
                                        <span class="badge bg-danger">Atrasada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Prazo curto</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('solicitacoes.show', $sol->id) }}" class="btn btn-light border btn-sm fw-semibold" id="btn-atencao-ver-{{ $sol->id }}">Detalhes</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-secondary">
                                    Nenhuma solicitação requer atenção imediata no momento. Bom trabalho!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SCRIPTS DE CONFIGURAÇÃO DOS GRÁFICOS (CHART.JS) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Paleta de Cores
            const colors = {
                navy: '#212a57',
                blue: '#079ae1',
                orange: '#f36229',
                green: '#10b981',
                gray: '#cbd5e1',
                navyLight: '#3b4c99'
            };

            const globalOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { family: 'Inter', size: 11 } }
                    }
                }
            };

            // Gráfico 1: Solicitações por Etapa (Barra)
            const ctxEtapas = document.getElementById('chartEtapas').getContext('2d');
            const dataEtapas = @json($graficoEtapas);
            new Chart(ctxEtapas, {
                type: 'bar',
                data: {
                    labels: dataEtapas.labels,
                    datasets: [{
                        label: 'Solicitações',
                        data: dataEtapas.data,
                        backgroundColor: colors.navy,
                        borderRadius: 6
                    }]
                },
                options: {
                    ...globalOptions,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });

            // Gráfico 2: Solicitações por Tipo (Doughnut)
            const ctxTipos = document.getElementById('chartTipos').getContext('2d');
            const dataTipos = @json($graficoTipos);
            new Chart(ctxTipos, {
                type: 'doughnut',
                data: {
                    labels: dataTipos.labels,
                    datasets: [{
                        data: dataTipos.data,
                        backgroundColor: [colors.blue, colors.navy]
                    }]
                },
                options: globalOptions
            });

            // Gráfico 3: Distribuição por Faixa (Barra Horizontal)
            const ctxFaixas = document.getElementById('chartFaixas').getContext('2d');
            const dataFaixas = @json($graficoFaixas);
            new Chart(ctxFaixas, {
                type: 'bar',
                data: {
                    labels: dataFaixas.labels,
                    datasets: [{
                        label: 'Quantidade',
                        data: dataFaixas.data,
                        backgroundColor: colors.blue,
                        borderRadius: 6
                    }]
                },
                options: {
                    ...globalOptions,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });

            // Gráfico 4: Solicitações por Recurso (Barra Horizontal)
            const ctxRecursos = document.getElementById('chartRecursos').getContext('2d');
            const dataRecursos = @json($graficoRecursos);
            new Chart(ctxRecursos, {
                type: 'bar',
                data: {
                    labels: dataRecursos.labels.length ? dataRecursos.labels : ['Sem dados'],
                    datasets: [{
                        label: 'Atribuídas',
                        data: dataRecursos.data.length ? dataRecursos.data : [0],
                        backgroundColor: colors.navyLight,
                        borderRadius: 6
                    }]
                },
                options: {
                    ...globalOptions,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });

            // Gráfico 5: Evolução Mensal (Linha)
            const ctxMensal = document.getElementById('chartMensal').getContext('2d');
            const dataMensal = @json($graficoMeses);
            new Chart(ctxMensal, {
                type: 'line',
                data: {
                    labels: dataMensal.labels,
                    datasets: [
                        {
                            label: 'Abertas',
                            data: dataMensal.abertas,
                            borderColor: colors.orange,
                            backgroundColor: 'rgba(243,98,41,0.05)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Concluídas',
                            data: dataMensal.concluidas,
                            borderColor: colors.green,
                            backgroundColor: 'rgba(16,185,129,0.05)',
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    ...globalOptions,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });

            // Gráfico 6: Cumprimento de Prazos (Pie)
            const ctxPrazos = document.getElementById('chartPrazos').getContext('2d');
            const dataPrazos = @json($graficoPrazosEntregas);
            new Chart(ctxPrazos, {
                type: 'pie',
                data: {
                    labels: dataPrazos.labels,
                    datasets: [{
                        data: dataPrazos.data,
                        backgroundColor: [colors.green, colors.orange]
                    }]
                },
                options: globalOptions
            });
        });
    </script>
@endsection
