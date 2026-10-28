@extends('layouts.app')

@section('title', 'Usuários - Gestão SAU & SD')

@section('breadcrumbs')
    <span class="breadcrumbs-item text-secondary">Usuários</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Gestão de Usuários</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Gerencie as credenciais, perfis e permissões dos gestores e recursos do sistema.</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary d-flex align-items-center gap-1" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-criar-usuario-link">
            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Novo Usuário
        </a>
    </div>

    <!-- BARRA DE FILTROS -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <div class="card-body p-4">
            <form action="{{ route('usuarios.index') }}" method="GET" class="row g-3 align-items-end">
                <!-- Busca por nome ou e-mail -->
                <div class="col-12 col-md-5">
                    <label for="busca" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Buscar por Nome / E-mail</label>
                    <input type="text" name="busca" id="busca" class="form-control form-control-sm" placeholder="Digite o nome ou e-mail..." value="{{ request('busca') }}">
                </div>

                <!-- Perfil -->
                <div class="col-6 col-md-3">
                    <label for="perfil" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Perfil de Acesso</label>
                    <select name="perfil" id="perfil" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="gestor" {{ request('perfil') === 'gestor' ? 'selected' : '' }}>Gestor</option>
                        <option value="recurso" {{ request('perfil') === 'recurso' ? 'selected' : '' }}>Recurso</option>
                    </select>
                </div>

                <!-- Ativo / Inativo -->
                <div class="col-6 col-md-2">
                    <label for="ativo" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Status</label>
                    <select name="ativo" id="ativo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativos</option>
                        <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>

                <!-- Botões -->
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-filtrar-usuarios">Filtrar</button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- LISTAGEM DE USUÁRIOS -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark" style="background-color: var(--navy);">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nome', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Usuário
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'nome')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                E-mail
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'email')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'perfil', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Perfil
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'perfil')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'ativo', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Status
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'ativo')
                                        {{ request('direction') === 'asc' ? '▲' : '▼' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Último Acesso</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                Data de Cadastro
                                <span style="font-size: 0.75rem;">
                                    @if(request('sort') === 'created_at')
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
                    @forelse($usuarios as $usr)
                        @php
                            $iniciais = collect(explode(' ', $usr->nome))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                        @endphp
                        <tr>
                            <!-- Usuário (Avatar + Nome) -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle fw-bold" style="width: 36px; height: 36px; font-size: 0.8rem; background-color: var(--navy);">
                                        {{ $iniciais }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $usr->nome }}</span>
                                </div>
                            </td>

                            <!-- E-mail -->
                            <td>{{ $usr->email }}</td>

                            <!-- Perfil -->
                            <td>
                                <x-badge :status="$usr->perfil" />
                            </td>

                            <!-- Status -->
                            <td>
                                @if($usr->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>

                            <!-- Último Acesso -->
                            <td>
                                @if($usr->ultimo_acesso_em)
                                    {{ $usr->ultimo_acesso_em->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-secondary italic" style="font-style: italic;">Nunca acessou</span>
                                @endif
                            </td>

                            <!-- Data Cadastro -->
                            <td>{{ $usr->created_at->format('d/m/Y H:i') }}</td>

                            <!-- Ações -->
                            <td style="text-align: center;">
                                <a href="{{ route('usuarios.edit', $usr->id) }}" class="btn btn-outline-primary btn-sm fw-semibold" id="btn-user-editar-{{ $usr->id }}">
                                    Editar / Senha
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                Nenhum usuário encontrado correspondente aos filtros.
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
                    {{ $usuarios->links() }}
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
