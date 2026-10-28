@extends('layouts.app')

@section('title', 'Lixeira de Solicitações - Gestão SAU & SD')

@section('breadcrumbs')
    <a href="{{ route('solicitacoes.index') }}" class="breadcrumbs-item text-secondary text-decoration-none">Solicitações</a>
    <span class="breadcrumbs-item text-secondary">Lixeira</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Lixeira de Solicitações (Excluídas)</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Consulte registros que foram removidos da listagem ativa. Você pode restaurá-los a qualquer momento.</p>
        </div>
        <a href="{{ route('solicitacoes.index') }}" class="btn btn-outline-secondary">
            Voltar para Ativas
        </a>
    </div>

    <!-- BARRA DE BUSCA NA LIXEIRA -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <div class="card-body p-4">
            <form action="{{ route('solicitacoes.lixeira') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-9 col-lg-10">
                    <label for="busca" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Buscar Excluídas</label>
                    <input type="text" name="busca" id="busca" class="form-control form-control-sm" placeholder="Número, descrição ou solicitante..." value="{{ request('busca') }}">
                </div>
                <div class="col-12 col-md-3 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-filtrar-lixeira">Buscar</button>
                    <a href="{{ route('solicitacoes.lixeira') }}" class="btn btn-outline-secondary btn-sm flex-fill">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABELA DE EXCLUÍDAS -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark" style="background-color: var(--navy);">
                    <tr>
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
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nome_solicitante', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Solicitante
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'nome_solicitante')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Descrição Resumida</th>
                        <th>Excluído Por</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'deleted_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Data de Exclusão
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'deleted_at')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Motivo da Exclusão</th>
                        <th style="text-align: center; width: 180px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitacoesExcluidas as $sol)
                        <tr>
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

                            <!-- Quem deletou -->
                            <td>{{ $sol->excluidoPor->nome ?? 'Sistema' }}</td>

                            <!-- Quando deletou -->
                            <td>{{ $sol->deleted_at->format('d/m/Y H:i') }}</td>

                            <!-- Motivo de exclusão -->
                            <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-style: italic;" title="{{ $sol->motivo_exclusao }}">
                                "{{ $sol->motivo_exclusao }}"
                            </td>

                            <!-- Ações -->
                            <td style="text-align: center;">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('solicitacoes.show', $sol->id) }}" class="btn btn-light border btn-sm fw-semibold" id="btn-trash-ver-{{ $sol->id }}">
                                        Detalhar
                                    </a>
                                    
                                    <form action="{{ route('solicitacoes.restore', $sol->id) }}" method="POST" onsubmit="return confirm('Deseja realmente restaurar a solicitação {{ $sol->identificacao }} para a listagem ativa?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm fw-semibold" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-trash-restaurar-{{ $sol->id }}">
                                            Restaurar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                Nenhuma solicitação excluída encontrada na lixeira.
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
                    {{ $solicitacoesExcluidas->links() }}
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
