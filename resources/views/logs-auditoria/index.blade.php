@extends('layouts.app')

@section('title', 'Logs de Auditoria - Gestão SAU & SD')

@section('breadcrumbs')
    <span class="breadcrumbs-item text-secondary">Auditoria</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Logs de Auditoria Funcional</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Monitore e rastreie todas as ações críticas executadas pelos usuários no sistema.</p>
        </div>
        <div>
            <form action="{{ route('logs-auditoria.limpar') }}" method="POST" onsubmit="return confirm('Deseja realmente limpar todos os logs de auditoria mais antigos que 24 horas? Esta ação não pode ser revertida.');">
                @csrf
                <button type="submit" class="btn fw-semibold d-flex align-items-center gap-1" style="color: #f36229; border: 1px solid #f36229; background-color: #ffffff;">
                    <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Limpar Logs (>24h)
                </button>
            </form>
        </div>
    </div>

    <!-- BARRA DE FILTROS AVANÇADOS -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <div class="card-body p-4">
            <form action="{{ route('logs-auditoria.index') }}" method="GET" class="row g-3 align-items-end">
                <!-- Texto Livre (Busca na descrição) -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label for="busca" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Buscar Descrição</label>
                    <input type="text" name="busca" id="busca" class="form-control form-control-sm" placeholder="Palavra-chave do log..." value="{{ request('busca') }}">
                </div>

                <!-- Usuário -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="usuario_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Autor da Ação</label>
                    <select name="usuario_id" id="usuario_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usr)
                            <option value="{{ $usr->id }}" {{ request('usuario_id') == $usr->id ? 'selected' : '' }}>{{ $usr->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ação -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="acao" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Ação</label>
                    <select name="acao" id="acao" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($acoesUnicas as $ac)
                            <option value="{{ $ac }}" {{ request('acao') === $ac ? 'selected' : '' }}>{{ $ac }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Entidade -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="entidade" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Entidade</label>
                    <select name="entidade" id="entidade" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($entidadesUnicas as $ent)
                            <option value="{{ $ent }}" {{ request('entidade') === $ent ? 'selected' : '' }}>{{ $ent }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ID Entidade -->
                <div class="col-6 col-md-2 col-lg-1">
                    <label for="entidade_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">ID Reg.</label>
                    <input type="number" name="entidade_id" id="entidade_id" class="form-control form-control-sm" placeholder="Ex: 5" value="{{ request('entidade_id') }}">
                </div>

                <!-- Endereço IP -->
                <div class="col-12 col-md-4 col-lg-2">
                    <label for="endereco_ip" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Endereço IP</label>
                    <input type="text" name="endereco_ip" id="endereco_ip" class="form-control form-control-sm" placeholder="Ex: 192.0.2.1" value="{{ request('endereco_ip') }}">
                </div>

                <!-- Data Inicial -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="data_inicial" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Data Inicial</label>
                    <input type="date" name="data_inicial" id="data_inicial" class="form-control form-control-sm" value="{{ request('data_inicial') }}">
                </div>

                <!-- Data Final -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="data_final" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Data Final</label>
                    <input type="date" name="data_final" id="data_final" class="form-control form-control-sm" value="{{ request('data_final') }}">
                </div>

                <!-- Botões -->
                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-filtrar-auditoria">Filtrar</button>
                    <a href="{{ route('logs-auditoria.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- LISTA DE LOGS -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark" style="background-color: var(--navy);">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Data e Hora
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'created_at' || !request('sort'))
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Usuário (Autor)</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'acao', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Ação
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'acao')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'entidade', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Entidade Afetada
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'entidade')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>ID Reg.</th>
                        <th>Descrição da Ação</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'endereco_ip', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Origem (IP)
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'endereco_ip')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th style="text-align: center; width: 120px;">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <!-- Data e hora -->
                            <td class="fw-semibold text-dark">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            
                            <!-- Autor -->
                            <td>
                                @if($log->usuario)
                                    <strong class="text-dark">{{ $log->usuario->nome }}</strong>
                                    <span class="d-block text-secondary" style="font-size: 0.75rem;">{{ $log->usuario->email }}</span>
                                @else
                                    <span class="text-secondary italic" style="font-style: italic;">Sistema / CLI</span>
                                @endif
                            </td>

                            <!-- Ação -->
                            <td>
                                <x-badge :status="$log->acao" />
                            </td>

                            <!-- Entidade -->
                            <td style="font-family: monospace; font-size:0.85rem;">{{ $log->entidade }}</td>

                            <!-- ID Reg. -->
                            <td>{{ $log->entidade_id ?? '-' }}</td>

                            <!-- Descrição -->
                            <td style="max-width: 300px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $log->descricao }}">
                                {{ $log->descricao }}
                            </td>

                            <!-- IP -->
                            <td style="font-family: monospace; font-size:0.85rem;">{{ $log->endereco_ip }}</td>

                            <!-- Ações -->
                            <td style="text-align: center;">
                                @if(!empty($log->dados_anteriores) || !empty($log->dados_novos))
                                    <a href="{{ route('logs-auditoria.show', $log->id) }}" class="btn btn-outline-primary btn-sm fw-semibold" id="btn-log-ver-{{ $log->id }}">
                                        Comparar
                                    </a>
                                @else
                                    <span class="text-secondary italic" style="font-size:0.8rem; font-style:italic;">Sem dados</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                Nenhum log de auditoria encontrado correspondente aos filtros.
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
                    {{ $logs->links() }}
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
