<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Gestão SAU & SD')</title>
    
    <!-- Metas SEO -->
    <meta name="description" content="Central de Solicitações SAU & SD - Sistema Interno de Gestão de Processos de Trabalho">
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS e JS Compilados pelo Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Biblioteca de Gráficos (Chart.js via CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- MENU LATERAL (Sidebar) -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-logo">
                <!-- Ícone SVG de Engrenagem e Prancheta -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
                <span>Gestão SAU & SD</span>
            </div>

            <ul class="sidebar-menu">
                <!-- Dashboard (Para todos) -->
                <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" id="menu-dashboard">
                        <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Listagem de Solicitações (Para todos) -->
                <li class="sidebar-item {{ Request::routeIs('solicitacoes.index') || Request::routeIs('solicitacoes.show') || Request::routeIs('solicitacoes.edit') ? 'active' : '' }}">
                    <a href="{{ route('solicitacoes.index') }}" id="menu-solicitacoes">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        <span>Solicitações</span>
                    </a>
                </li>

                <!-- Quadro Kanban (Para todos) -->
                <li class="sidebar-item {{ Request::routeIs('kanban.index') ? 'active' : '' }}">
                    <a href="{{ route('kanban.index') }}" id="menu-kanban">
                        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7V7h2v10zm4-4h-2V7h2v6zm4 2h-2V7h2v8z"/></svg>
                        <span>Quadro Kanban</span>
                    </a>
                </li>

                <!-- Prazos e Cronograma (Para todos) -->
                <li class="sidebar-item {{ Request::routeIs('prazos.index') ? 'active' : '' }}">
                    <a href="{{ route('prazos.index') }}" id="menu-prazos">
                        <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                        <span>Prazos</span>
                    </a>
                </li>

                <!-- Gestão de Usuários (Exclusivo Recurso) -->
                @if(Auth::user()->isRecurso())
                <li class="sidebar-item {{ Request::routeIs('usuarios.index') || Request::routeIs('usuarios.create') || Request::routeIs('usuarios.edit') ? 'active' : '' }}">
                    <a href="{{ route('usuarios.index') }}" id="menu-usuarios">
                        <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                        <span>Usuários</span>
                    </a>
                </li>

                <!-- Sessões Ativas (Exclusivo Recurso) -->
                <li class="sidebar-item {{ Request::routeIs('usuarios.sessoes') ? 'active' : '' }}">
                    <a href="{{ route('usuarios.sessoes') }}" id="menu-sessoes">
                        <svg viewBox="0 0 24 24" style="fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 10h3l-4 4-4-4h3V8h2v4z"/></svg>
                        <span>Sessões Ativas</span>
                    </a>
                </li>

                <!-- Solicitações Excluídas / Lixeira (Exclusivo Recurso) -->
                <li class="sidebar-item {{ Request::routeIs('solicitacoes.lixeira') ? 'active' : '' }}">
                    <a href="{{ route('solicitacoes.lixeira') }}" id="menu-lixeira">
                        <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        <span>Lixeira</span>
                    </a>
                </li>

                <!-- Logs de Auditoria (Exclusivo Recurso) -->
                <li class="sidebar-item {{ Request::routeIs('logs-auditoria.*') ? 'active' : '' }}">
                    <a href="{{ route('logs-auditoria.index') }}" id="menu-auditoria">
                        <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                        <span>Auditoria</span>
                    </a>
                </li>
                @endif

                <!-- Minha Conta (Para todos) -->
                <li class="sidebar-item {{ Request::routeIs('minha-conta.index') ? 'active' : '' }}">
                    <a href="{{ route('minha-conta.index') }}" id="menu-minha-conta">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                        <span>Minha Conta</span>
                    </a>
                </li>
            </ul>

            <!-- Rodapé do Sidebar com Dados do Usuário e Logout -->
            <div class="sidebar-footer">
                <x-avatar :name="Auth::user()->nome" />
                <div class="user-info-block">
                    <span class="user-info-name" title="{{ Auth::user()->nome }}">{{ Auth::user()->nome }}</span>
                    <span class="user-info-role">{{ Auth::user()->perfil }}</span>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" style="margin-left: auto;">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" id="btn-logout" title="Sair" style="color: rgba(255,255,255,0.7); min-width: auto; padding: 4px;">
                        <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.58-5.42L6.17 5.17C4.23 6.82 3 9.26 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- CONTAINER DA DIREITA -->
        <div class="main-wrapper">
            <!-- Cabeçalho (Header) -->
            <header class="main-header">
                <div class="header-left">
                    <button id="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Recolher menu">
                        <!-- Ícone de Menu Hamburger -->
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                        </svg>
                    </button>
                    
                    <!-- BREADCRUMBS -->
                    <nav class="breadcrumbs">
                        <a href="{{ route('dashboard') }}" class="breadcrumbs-item">Início</a>
                        @yield('breadcrumbs')
                    </nav>
                </div>

                <div class="header-right" style="display: flex; align-items: center;">
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-secondary);">
                        {{ now()->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                    </span>
                </div>
            </header>

            <!-- CONTEÚDO PRINCIPAL (Main) -->
            <main class="content-body">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- CONTAINER DE TOASTS (Notificações Componentizadas) -->
    <x-toast />

    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO LOGICA (Global) -->
    @if(Auth::user()->isRecurso())
    <div id="modal-excluir-solicitacao" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title">Excluir Solicitação</h3>
                <button type="button" class="modal-close-btn" onclick="fecharModalExcluir()" aria-label="Fechar">
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>
            <form id="form-excluir-solicitacao" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p style="margin-bottom: 1rem; font-size: 0.95rem;">
                        Você está prestes a excluir a solicitação <strong id="excluir-identificacao-span" style="color:var(--orange-alert);"></strong>. Esta ação é reversível através da Lixeira de solicitações.
                    </p>
                    <div class="form-group" style="min-width: 100%;">
                        <label for="motivo_exclusao" class="form-label">Motivo da Exclusão <span style="color:var(--orange-alert);">*</span></label>
                        <textarea name="motivo_exclusao" id="motivo_exclusao" class="form-control" placeholder="Descreva brevemente o motivo de exclusão..." required minlength="5" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModalExcluir()">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de Redimensionamento Manual de Colunas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                const cols = table.querySelectorAll('th');
                cols.forEach(col => {
                    // Não colocar redimensionador em colunas muito estreitas como checkbox de seleção
                    if (col.offsetWidth < 50) return;

                    const resizer = document.createElement('div');
                    resizer.classList.add('resize-handle');
                    
                    col.style.position = 'relative';
                    col.appendChild(resizer);
                    
                    let x = 0;
                    let w = 0;
                    
                    const mouseDownHandler = function(e) {
                        x = e.clientX;
                        w = col.getBoundingClientRect().width;
                        
                        document.addEventListener('mousemove', mouseMoveHandler);
                        document.addEventListener('mouseup', mouseUpHandler);
                        
                        resizer.classList.add('resizing');
                        col.style.userSelect = 'none';
                    };
                    
                    const mouseMoveHandler = function(e) {
                        const dx = e.clientX - x;
                        col.style.width = `${w + dx}px`;
                    };
                    
                    const mouseUpHandler = function() {
                        document.removeEventListener('mousemove', mouseMoveHandler);
                        document.removeEventListener('mouseup', mouseUpHandler);
                        resizer.classList.remove('resizing');
                        col.style.userSelect = '';
                    };
                    
                    resizer.addEventListener('mousedown', mouseDownHandler);
                });
            });
        });
    </script>
</body>
</html>
