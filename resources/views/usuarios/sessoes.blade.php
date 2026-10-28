@extends('layouts.app')

@section('title', 'Sessões Ativas - Gestão SAU & SD')

@section('breadcrumbs')
    <a href="{{ route('usuarios.index') }}" class="breadcrumbs-item text-secondary text-decoration-none">Usuários</a>
    <span class="breadcrumbs-item text-secondary">Sessões Ativas</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Gerenciamento de Sessões Ativas</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Monitore e encerre conexões de usuários logados no sistema em tempo real.</p>
        </div>
    </div>

    <!-- SEÇÃO DE TABELA DE SESSÕES -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark" style="background-color: var(--navy);">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'user_nome', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Usuário
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'user_nome')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'ip_address', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Endereço IP
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'ip_address')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Navegador / Plataforma</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'last_activity', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Última Atividade
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'last_activity' || !request('sort'))
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th style="text-align: center; width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentSessionId = request()->session()->getId();
                    @endphp
                    @forelse($sessoes as $sessao)
                        @php
                            $lastActive = \Carbon\Carbon::createFromTimestamp($sessao->last_activity)->timezone('America/Sao_Paulo');
                            $isCurrent = $sessao->id === $currentSessionId;
                        @endphp
                        <tr>
                            <!-- Usuário -->
                            <td>
                                @if($sessao->user_nome)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center text-white rounded-circle fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem; background-color: var(--navy);">
                                            {{ mb_substr($sessao->user_nome, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $sessao->user_nome }}</span>
                                            <span class="text-secondary d-block" style="font-size: 0.75rem;">{{ $sessao->user_email }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-secondary italic" style="font-style: italic;">Convidado / Não autenticado</span>
                                @endif
                            </td>
                            
                            <!-- IP -->
                            <td style="font-family: monospace; font-size: 0.9rem;">
                                {{ $sessao->ip_address ?? 'Desconhecido' }}
                            </td>
                            
                            <!-- User Agent -->
                            <td class="text-secondary text-truncate" style="max-width: 300px; font-size: 0.8rem;" title="{{ $sessao->user_agent }}">
                                {{ $sessao->user_agent ?? 'Desconhecido' }}
                            </td>
                            
                            <!-- Última Atividade -->
                            <td>
                                {{ $lastActive->format('d/m/Y H:i:s') }}
                                <span class="text-secondary d-block" style="font-size: 0.75rem;">
                                    ({{ $lastActive->diffForHumans() }})
                                </span>
                            </td>
                            
                            <!-- Ações -->
                            <td style="text-align: center;">
                                @if($isCurrent)
                                    <span class="badge bg-success">Sessão Atual</span>
                                @else
                                    <form action="{{ route('usuarios.revogar-sessao', $sessao->id) }}" method="POST" onsubmit="return confirm('Deseja realmente derrubar esta sessão? O usuário será deslogado imediatamente.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm fw-semibold">
                                            Revogar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                Nenhuma sessão ativa registrada no banco de dados.
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
                    {{ $sessoes->links() }}
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
