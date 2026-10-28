@extends('layouts.app')

@section('title', 'Detalhes da Solicitação ' . $solicitacao->identificacao . ' - Gestão SAU & SD')

@section('breadcrumbs')
    <a href="{{ route('solicitacoes.index') }}" class="breadcrumbs-item">Solicitações</a>
    <span class="breadcrumbs-item">Detalhes</span>
@endsection

@section('content')
    <!-- CABEÇALHO DE AÇÕES -->
    <div class="page-header-row">
        <div class="page-title-group">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <h2 style="font-size: 2rem;">{{ $solicitacao->identificacao }}</h2>
                <span class="badge {{ $solicitacao->situacao_badge_class }}">{{ $solicitacao->situacao_calculada }}</span>
            </div>
            <p style="margin-top: 0.25rem; font-size: 0.95rem; font-weight: 500; color: var(--navy);">
                Solicitante: <strong style="color:var(--text-primary);">{{ $solicitacao->nome_solicitante }}</strong>
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('solicitacoes.index') }}" class="btn btn-secondary">
                Voltar
            </a>

            @if(Auth::user()->isRecurso())
                <a href="{{ route('solicitacoes.edit', $solicitacao->id) }}" class="btn btn-primary" id="btn-show-editar">
                    <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    Editar
                </a>
                <button type="button" class="btn btn-danger" id="btn-show-excluir" onclick="abrirModalExcluir({{ $solicitacao->id }}, '{{ $solicitacao->identificacao }}')">
                    <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Excluir
                </button>
            @endif
        </div>
    </div>

    <!-- LINHA DE ETAPAS VISUAL -->
    <div class="card-section" style="margin-bottom: 2rem; overflow-x: auto;">
        <h3 style="font-size: 1rem; color: var(--text-secondary); text-transform: uppercase; font-weight:600; margin-bottom: 1.5rem;">
            Progresso pelas Etapas do Ciclo de Vida
        </h3>
        
        @php
            $etapaOrdem = $solicitacao->etapa->ordem;
            $percentFill = min(100, max(0, ($etapaOrdem - 1) * 20)); // 6 etapas, 5 intervalos de 20%
        @endphp
        
        <div class="steps-progress-row" style="min-width: 800px;">
            <!-- Trilho preenchido dinamicamente -->
            <div class="steps-progress-bar-fill" style="width: {{ $percentFill }}%;"></div>

            <!-- Etapa 1: Ideia Registrada -->
            <div class="step-node {{ $etapaOrdem > 1 ? 'completed' : '' }} {{ $etapaOrdem == 1 ? 'active' : '' }}">
                <div class="step-circle">1</div>
                <span class="step-name">Ideia Registrada</span>
            </div>

            <!-- Etapa 2: Levantamento -->
            <div class="step-node {{ $etapaOrdem > 2 ? 'completed' : '' }} {{ $etapaOrdem == 2 ? 'active' : '' }}">
                <div class="step-circle">2</div>
                <span class="step-name">Levantamento</span>
            </div>

            <!-- Etapa 3: Processo de Software -->
            <div class="step-node {{ $etapaOrdem > 3 ? 'completed' : '' }} {{ $etapaOrdem == 3 ? 'active' : '' }}">
                <div class="step-circle">3</div>
                <span class="step-name">Processo Software</span>
            </div>

            <!-- Etapa 4: Homologação -->
            <div class="step-node {{ $etapaOrdem > 4 ? 'completed' : '' }} {{ $etapaOrdem == 4 ? 'active' : '' }}">
                <div class="step-circle">4</div>
                <span class="step-name">Homologação</span>
            </div>

            <!-- Etapa 5: Aplicação -->
            <div class="step-node {{ $etapaOrdem > 5 ? 'completed' : '' }} {{ $etapaOrdem == 5 ? 'active' : '' }}">
                <div class="step-circle">5</div>
                <span class="step-name">Aplicação</span>
            </div>

            <!-- Etapa 6: Concluído -->
            <div class="step-node {{ $etapaOrdem == 6 ? 'active' : '' }}">
                <div class="step-circle">6</div>
                <span class="step-name">Concluído</span>
            </div>
        </div>
    </div>

    <!-- INFORMAÇÕES DETALHADAS -->
    <div class="dashboard-grid" style="grid-template-columns: 2fr 1fr; align-items: start;">
        
        <!-- COLUNA DA ESQUERDA: DESCRIÇÃO, CHIPS E TIMELINE -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Descrição e Escopo -->
            <div class="card-section">
                <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                    Descrição da Demanda
                </h3>
                <div style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6; color: var(--text-secondary);">
                    {{ $solicitacao->descricao }}
                </div>
            </div>

            <!-- Observações dos Recursos (Exclusivo Administradores/Recursos) -->
            @if(Auth::user()->isRecurso() && $solicitacao->observacoes)
                <div class="card-section">
                    <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                        Observações dos Recursos
                    </h3>
                    <div style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6; color: var(--text-secondary);">
                        {{ $solicitacao->observacoes }}
                    </div>
                </div>
            @endif

            <!-- Histórico de Alterações / Linha do Tempo (Exclusivo Administradores/Recursos) -->
            @if(Auth::user()->isRecurso())
            <div class="card-section">
                <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">
                    Histórico de Acompanhamento (Timeline)
                </h3>

                <div class="timeline-vertical">
                    @forelse($logsLinhaTempo as $log)
                        @php
                            $isExclusao = $log->acao === 'Exclusão lógica' || $log->acao === 'Tentativa de acesso não autorizado';
                            $isCriacao = $log->acao === 'Cadastro de solicitação';
                            $classEvent = $isExclusao ? 'danger' : ($isCriacao ? 'active' : '');
                        @endphp
                        <div class="timeline-event {{ $classEvent }}">
                            <div class="timeline-event-marker"></div>
                            <div class="timeline-event-content">
                                <strong style="color:var(--navy); font-size:0.9rem;">{{ $log->acao }}</strong>
                                <p style="margin-top: 0.25rem; color: var(--text-secondary); font-size:0.85rem;">
                                    {{ $log->descricao }}
                                </p>
                                
                                @if(Auth::user()->isRecurso() && (!empty($log->dados_anteriores) || !empty($log->dados_novos)))
                                    @php
                                        $dadosNovos = is_array($log->dados_novos) ? $log->dados_novos : (is_string($log->dados_novos) ? json_decode($log->dados_novos, true) : []);
                                        $dadosAnteriores = is_array($log->dados_anteriores) ? $log->dados_anteriores : (is_string($log->dados_anteriores) ? json_decode($log->dados_anteriores, true) : []);
                                        $dadosNovos = is_array($dadosNovos) ? $dadosNovos : [];
                                        $dadosAnteriores = is_array($dadosAnteriores) ? $dadosAnteriores : [];
                                    @endphp
                                    @if(!empty($dadosNovos))
                                    <!-- Para Recursos, exibe as alterações específicas de forma sutil -->
                                    <div style="margin-top: 0.5rem; font-family: monospace; font-size: 0.75rem; color: var(--text-muted); background: #ffffff; padding: 0.4rem 0.6rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                        <strong>Campos modificados:</strong>
                                        @foreach($dadosNovos as $campo => $novoValor)
                                            @php
                                                $antigoValor = $dadosAnteriores[$campo] ?? 'Nenhum';
                                                // Sanitiza arrays se existirem
                                                if (is_array($novoValor)) $novoValor = json_encode($novoValor);
                                                if (is_array($antigoValor)) $antigoValor = json_encode($antigoValor);
                                            @endphp
                                            <div style="margin-top: 0.15rem;">
                                                <span style="color: var(--blue-primary);">{{ $campo }}</span>: 
                                                <del style="color: var(--orange-alert);">{{ $antigoValor }}</del> ➜ 
                                                <ins style="color: var(--color-success); text-decoration: none; font-weight: 600;">{{ $novoValor }}</ins>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                @endif
                                
                                <div class="timeline-event-meta">
                                    <span>Realizado por: <strong>{{ $log->usuario->nome ?? 'Sistema' }}</strong></span>
                                    <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--text-muted); font-size:0.9rem; text-align:center;">
                            Nenhum log funcional foi registrado para esta solicitação.
                        </p>
                    @endforelse
                </div>
            </div>
            @endif

        </div>

        <!-- COLUNA DA DIREITA: INFORMAÇÕES DE STATUS E CRONOGRAMA -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Cards de Progresso Rápido -->
            <div class="card-section" style="background-color: var(--navy); color: var(--white);">
                <h3 style="font-size: 1rem; color: rgba(255,255,255,0.7); text-transform: uppercase; font-weight:600; margin-bottom: 0.75rem;">
                    Status do Progresso
                </h3>
                <div style="display:flex; align-items: baseline; gap:0.25rem; margin-bottom: 0.5rem;">
                    <span style="font-family: var(--font-headings); font-size: 3rem; font-weight:800; line-height:1;">
                        {{ $solicitacao->percentual_conclusao }}%
                    </span>
                    <span style="font-size:1rem; opacity:0.8;">concluído</span>
                </div>
                <div style="background-color: rgba(255,255,255,0.15); width: 100%; height: 8px; border-radius: var(--radius-full); overflow: hidden; margin-bottom: 0.75rem;">
                    <div style="background-color: var(--blue-primary); width: {{ $solicitacao->percentual_conclusao }}%; height: 100%;"></div>
                </div>
                <p style="font-size: 0.8rem; color: rgba(255,255,255,0.85); line-height: 1.4;">
                    {{ $solicitacao->mensagem_progresso }}
                </p>
            </div>

            <!-- Cronograma e Prazos -->
            <div class="card-section">
                <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                    Datas e Metas
                </h3>
                
                <div style="display:flex; flex-direction:column; gap: 0.85rem; font-size: 0.9rem;">
                    <!-- Abertura -->
                    <div>
                        <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Data de Abertura</span>
                        <strong style="color:var(--navy);">{{ $solicitacao->data_abertura->format('d/m/Y H:i') }}</strong>
                    </div>

                    <!-- Prazo -->
                    <div>
                        <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Data de Prazo (Estimada)</span>
                        <strong style="color:var(--navy);">
                            @if($solicitacao->data_prazo)
                                {{ $solicitacao->data_prazo->format('d/m/Y H:i') }}
                            @else
                                <span class="text-secondary" style="font-style: italic;">Não aplicável (SAU)</span>
                            @endif
                        </strong>
                    </div>

                    <!-- Entrega -->
                    <div>
                        <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Data de Entrega (Real)</span>
                        @if($solicitacao->data_entrega)
                            <strong style="color:var(--color-success);">{{ $solicitacao->data_entrega->format('d/m/Y H:i') }}</strong>
                        @else
                            <span style="color:var(--text-muted); font-style:italic;">Não finalizada</span>
                        @endif
                    </div>

                    <!-- Tempo Transcorrido / Dias Restantes -->
                    <div style="border-top:1px dashed var(--border-color); padding-top:0.75rem;">
                        <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Tempo Restante / Atraso</span>
                        <strong style="color:{{ $solicitacao->is_atrasada ? 'var(--orange-alert)' : 'var(--navy)' }}; font-size:0.95rem;">
                            {{ $solicitacao->tempo_restante_texto }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Recursos Responsáveis -->
            <div class="card-section">
                <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                    Recursos Responsáveis
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($solicitacao->recursos as $rec)
                        @php
                            $iniciais = collect(explode(' ', $rec->nome))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                        @endphp
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <div class="user-avatar-initials" style="width:36px; height:36px; font-size:0.8rem; background-color: var(--navy);">
                                {{ $iniciais }}
                            </div>
                            <div style="display:flex; flex-direction:column; line-height:1.2;">
                                <strong style="font-size:0.85rem; color:var(--navy);">{{ $rec->nome }}</strong>
                                <span style="font-size:0.75rem; color:var(--text-muted);">{{ $rec->email }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Criador e Modificador -->
            <div class="card-section" style="font-size: 0.8rem; color: var(--text-muted); display:flex; flex-direction:column; gap:0.4rem;">
                <div>Registrado por: <strong>{{ $solicitacao->criadoPor->nome ?? 'Sistema' }}</strong> em {{ $solicitacao->created_at->format('d/m/Y H:i') }}</div>
                @if($solicitacao->atualizadoPor)
                    <div>Última alteração: <strong>{{ $solicitacao->atualizadoPor->nome }}</strong> em {{ $solicitacao->updated_at->format('d/m/Y H:i') }}</div>
                @endif
            </div>

        </div>
    </div>
@endsection
