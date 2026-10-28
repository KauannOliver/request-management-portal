@extends('layouts.app')

@section('title', 'Detalhes da Auditoria - Gestão SAU & SD')

@section('breadcrumbs')
    <a href="{{ route('logs-auditoria.index') }}" class="breadcrumbs-item">Auditoria</a>
    <span class="breadcrumbs-item">Detalhes</span>
@endsection

@section('content')
    <div class="page-header-row">
        <div class="page-title-group">
            <h2>Comparativo de Auditoria</h2>
            <p>Compare as alterações de valores cadastrais para este registro.</p>
        </div>
        <a href="{{ route('logs-auditoria.index') }}" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <!-- INFORMAÇÕES DO LOG -->
    <div class="card-section" style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
            Informações Gerais da Ação
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem;">
            <div>
                <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Data e Hora</span>
                <strong style="color:var(--navy);">{{ $log->created_at->format('d/m/Y H:i:s') }}</strong>
            </div>

            <div>
                <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Autor (Usuário)</span>
                <strong style="color:var(--navy);">{{ $log->usuario->nome ?? 'Sistema / CLI' }}</strong>
                @if($log->usuario)
                    <span style="font-size:0.75rem; color:var(--text-muted); display:block;">{{ $log->usuario->email }}</span>
                @endif
            </div>

            <div>
                <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Ação Efetuada</span>
                <span class="badge badge-em-andamento" style="margin-top:0.25rem;">{{ $log->acao }}</span>
            </div>

            <div>
                <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Origem (IP)</span>
                <strong style="color:var(--navy); font-family: monospace;">{{ $log->endereco_ip }}</strong>
            </div>

            <div>
                <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Entidade / Tabela</span>
                <strong style="color:var(--navy); font-family: monospace;">{{ $log->entidade }}</strong>
            </div>

            <div>
                <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase;">Identificador Registro (ID)</span>
                <strong style="color:var(--navy);">{{ $log->entidade_id ?? '-' }}</strong>
            </div>
        </div>

        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--border-color); font-size: 0.9rem;">
            <span style="display:block; color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase; margin-bottom: 0.25rem;">Descrição da Auditoria</span>
            <span style="color: var(--text-secondary); font-weight: 500;">{{ $log->descricao }}</span>
        </div>

        <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">
            <strong>Dispositivo / User Agent:</strong> {{ $log->user_agent }}
        </div>
    </div>

    <!-- COMPARATIVO DE ALTERAÇÕES -->
    <div class="card-section">
        <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--blue-primary);">
            Campos Modificados (Comparativo De - Para)
        </h3>

        @if(!empty($log->dados_novos))
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Atributo / Campo</th>
                            <th style="width: 37.5%;">Valor Anterior (De)</th>
                            <th style="width: 37.5%;">Novo Valor (Para)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($log->dados_novos as $campo => $novoValor)
                            @php
                                $valAntigo = $log->dados_anteriores[$campo] ?? null;
                                
                                // Formata booleanos para leitura legível
                                if (is_bool($novoValor)) $novoValor = $novoValor ? 'Sim/Ativo' : 'Não/Inativo';
                                if (is_bool($valAntigo)) $valAntigo = $valAntigo ? 'Sim/Ativo' : 'Não/Inativo';

                                // Formata nulos
                                if (is_null($novoValor)) $novoValor = 'Vazio/Nulo';
                                if (is_null($valAntigo)) $valAntigo = 'Vazio/Nulo';

                                // Trata arrays ou objetos json
                                if (is_array($novoValor)) $novoValor = json_encode($novoValor, JSON_PRETTY_PRINT);
                                if (is_array($valAntigo)) $valAntigo = json_encode($valAntigo, JSON_PRETTY_PRINT);
                            @endphp
                            <tr>
                                <td style="font-weight: 700; color: var(--navy); font-family: monospace;">{{ $campo }}</td>
                                <td style="background-color: rgba(243,98,41,0.03);">
                                    <span class="diff-highlight" style="font-family: monospace; white-space: pre-wrap; font-size: 0.85rem;">
                                        {{ $valAntigo }}
                                    </span>
                                </td>
                                <td style="background-color: rgba(16,185,129,0.03);">
                                    <span style="font-family: monospace; white-space: pre-wrap; font-size: 0.85rem; color: var(--color-success); font-weight: 600; background-color: rgba(16,185,129,0.1); padding: 2px 4px; border-radius: 2px;">
                                        {{ $novoValor }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-muted); padding: 2rem;">
                Não existem dados cadastrais adicionais gravados para este log.
            </p>
        @endif
    </div>
@endsection
