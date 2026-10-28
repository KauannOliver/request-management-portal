@extends('layouts.app')

@section('title', 'Editar Solicitação ' . $solicitacao->identificacao . ' - Gestão SAU & SD')

@section('breadcrumbs')
    <a href="{{ route('solicitacoes.index') }}" class="breadcrumbs-item">Solicitações</a>
    <span class="breadcrumbs-item">Editar</span>
@endsection

@section('content')
    <div class="page-header-row">
        <div class="page-title-group">
            <h2>Editar Solicitação: {{ $solicitacao->identificacao }}</h2>
            <p>Atualize as informações, prazos, recursos e etapas desta demanda.</p>
        </div>
        <a href="{{ route('solicitacoes.show', $solicitacao->id) }}" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <form action="{{ route('solicitacoes.update', $solicitacao->id) }}" method="POST" class="form-confirm-leave">
        @csrf
        @method('PUT')

        <div class="dashboard-grid" style="grid-template-columns: 2fr 1fr; align-items: start;">
            <!-- SEÇÃO PRINCIPAL (ESQUERDA) -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- IDENTIFICAÇÃO E SOLICITANTE -->
                <div class="card-section">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                        1. Identificação de Negócio
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <!-- Tipo -->
                        <div class="form-group">
                            <label for="tipo" class="form-label">Tipo <span style="color:var(--orange-alert);">*</span></label>
                            <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                                <option value="SAU" {{ old('tipo', $solicitacao->tipo) === 'SAU' ? 'selected' : '' }}>SAU</option>
                                <option value="SD" {{ old('tipo', $solicitacao->tipo) === 'SD' ? 'selected' : '' }}>SD</option>
                            </select>
                            @error('tipo') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Número -->
                        <div class="form-group">
                            <label for="numero" class="form-label">Número <span style="color:var(--orange-alert);">*</span></label>
                            <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" placeholder="Ex: 1025" value="{{ old('numero', $solicitacao->numero) }}" required>
                            @error('numero') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Nome Solicitante -->
                    <div class="form-group" style="min-width: 100%;">
                        <label for="nome_solicitante" class="form-label">Nome do Solicitante <span style="color:var(--orange-alert);">*</span></label>
                        <input type="text" name="nome_solicitante" id="nome_solicitante" class="form-control @error('nome_solicitante') is-invalid @enderror" placeholder="Ex: João da Silva" value="{{ old('nome_solicitante', $solicitacao->nome_solicitante) }}" required>
                        <span style="font-size:0.75rem; color:var(--text-muted);">Apenas o nome legível de quem solicitou o serviço (não vinculado a perfis do sistema).</span>
                        @error('nome_solicitante') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- DESCRIÇÃO -->
                <div class="card-section">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                        2. Detalhamento
                    </h3>
                    <div class="form-group" style="min-width: 100%; margin-bottom: 1rem;">
                        <label for="descricao" class="form-label">Descrição Ampla da Demanda <span style="color:var(--orange-alert);">*</span></label>
                        <textarea name="descricao" id="descricao" class="form-control @error('descricao') is-invalid @enderror" placeholder="Descreva os requisitos técnicos, regras de negócio ou tarefas a serem realizadas..." required>{{ old('descricao', $solicitacao->descricao) }}</textarea>
                        <div style="display:flex; justify-content:space-between; margin-top:0.25rem;">
                            @error('descricao') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                            <span id="char-counter" style="font-size:0.75rem; color:var(--text-muted); margin-left:auto;">0 caracteres</span>
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div class="form-group" style="min-width: 100%;">
                        <label for="observacoes" class="form-label">Observações dos Recursos</label>
                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" placeholder="Adicione observações, notas ou comentários adicionais sobre esta demanda..." rows="3">{{ old('observacoes', $solicitacao->observacoes) }}</textarea>
                        @error('observacoes') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- ANDAMENTO E PROGRESsO (SLIDER) -->
                <div class="card-section">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                        3. Fluxo de Trabalho & Progresso
                    </h3>
                    
                    <div class="form-group" style="min-width: 100%; margin-bottom: 1.5rem;">
                        <label for="etapa_id" class="form-label">Etapa de Trabalho <span style="color:var(--orange-alert);">*</span></label>
                        <select name="etapa_id" id="etapa_id" data-warn-retrocesso="true" class="form-control @error('etapa_id') is-invalid @enderror" required>
                            @foreach($etapas as $et)
                                <option value="{{ $et->id }}" data-ordem="{{ $et->ordem }}" {{ old('etapa_id', $solicitacao->etapa_id) == $et->id ? 'selected' : '' }}>
                                    Etapa {{ $et->ordem }} - {{ $et->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('etapa_id') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- SLIDER PREMIUM DE PERCENTUAL -->
                    <div class="form-group" style="min-width: 100%;">
                        <label class="form-label">Status de Conclusão <span style="color:var(--orange-alert);">*</span></label>
                        
                        <div class="custom-slider-wrapper">
                            <div class="slider-labels">
                                <span>Não Iniciado</span>
                                <span>Em Andamento</span>
                                <span>Concluído</span>
                            </div>
                            
                            <div class="slider-container">
                                <input type="range" id="percentual_conclusao_slider" class="range-slider" min="0" max="100" step="1" value="{{ old('percentual_conclusao', $solicitacao->percentual_conclusao) }}" aria-valuemin="0" aria-valuemax="100">
                                <input type="hidden" name="percentual_conclusao" id="percentual_conclusao" value="{{ old('percentual_conclusao', $solicitacao->percentual_conclusao) }}">
                            </div>

                            <div class="slider-ticks">
                                <span class="slider-tick" data-value="0">0%</span>
                                <span class="slider-tick" data-value="25">25%</span>
                                <span class="slider-tick" data-value="50">50%</span>
                                <span class="slider-tick" data-value="75">75%</span>
                                <span class="slider-tick" data-value="100">100%</span>
                            </div>

                            <div class="slider-info-box">
                                <span class="slider-value-display" id="slider-val-display">{{ old('percentual_conclusao', $solicitacao->percentual_conclusao) }}%</span>
                                <span class="slider-desc-display" id="slider-desc-display">{{ $solicitacao->mensagem_progresso }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUNA LATERAL (DIREITA) -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- RESPONSÁVEIS (MULTISELECT MODERNO) -->
                <div class="card-section">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                        4. Recursos Responsáveis <span style="color:var(--orange-alert);">*</span>
                    </h3>
                    
                    <div class="form-group" style="min-width: 100%;">
                        <label class="form-label" style="margin-bottom: 0.5rem;">Responsáveis Vinculados</label>
                        
                        <div class="custom-multiselect" id="recursos-trigger">
                            <div id="recursos-chips-container" class="chips-container">
                                <!-- Preenchido dinamicamente via JS -->
                            </div>
                            
                            <div class="multiselect-dropdown" id="recursos-dropdown">
                                <div style="padding: 0.5rem; border-bottom: 1px solid var(--border-color); position: sticky; top:0; background:white; z-index:5;">
                                    <input type="text" id="recursos-search" class="form-control" placeholder="Buscar por nome ou e-mail..." style="height: 32px; font-size: 0.8rem;">
                                </div>
                                @foreach($recursos as $rec)
                                    <label class="multiselect-option" data-nome="{{ $rec->nome }}" data-email="{{ $rec->email }}">
                                        <input type="checkbox" name="recursos[]" value="{{ $rec->id }}" class="recurso-checkbox" data-nome="{{ $rec->nome }}" {{ in_array($rec->id, old('recursos', $recursosAssociados)) ? 'checked' : '' }} style="cursor: pointer; width:16px; height:16px;">
                                        <div style="display: flex; flex-direction: column; line-height: 1.2;">
                                            <span style="font-size: 0.85rem; font-weight:600; color:var(--navy);">{{ $rec->nome }}</span>
                                            <span style="font-size: 0.75rem; color:var(--text-muted);">{{ $rec->email }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('recursos') <span style="color:var(--orange-alert); font-size:0.8rem; display:block; margin-top:0.25rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- DATAS -->
                <div class="card-section">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                        5. Cronograma
                    </h3>

                    <!-- Data Abertura -->
                    <div class="form-group" style="min-width: 100%; margin-bottom: 1rem;">
                        <label for="data_abertura" class="form-label">Data de Abertura <span style="color:var(--orange-alert);">*</span></label>
                        <input type="datetime-local" name="data_abertura" id="data_abertura" class="form-control @error('data_abertura') is-invalid @enderror" value="{{ old('data_abertura', $solicitacao->data_abertura->format('Y-m-d\TH:i')) }}" required>
                        @error('data_abertura') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Data Prazo -->
                    <div class="form-group" style="min-width: 100%; margin-bottom: 1rem;">
                        <label for="data_prazo" class="form-label">Data de Prazo (Estimado) <span style="color:var(--orange-alert);">*</span></label>
                        <input type="datetime-local" name="data_prazo" id="data_prazo" class="form-control @error('data_prazo') is-invalid @enderror" value="{{ old('data_prazo', $solicitacao->data_prazo ? $solicitacao->data_prazo->format('Y-m-d\TH:i') : '') }}" required>
                        @error('data_prazo') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Data Entrega (Somente se for 100%, controlado por JS e backend) -->
                    <div class="form-group" id="data-entrega-container" style="min-width: 100%; display: {{ old('percentual_conclusao', $solicitacao->percentual_conclusao) == 100 ? 'block' : 'none' }};">
                        <label for="data_entrega" class="form-label">Data de Entrega (Real)</label>
                        <input type="datetime-local" name="data_entrega" id="data_entrega" class="form-control @error('data_entrega') is-invalid @enderror" value="{{ old('data_entrega', $solicitacao->data_entrega ? $solicitacao->data_entrega->format('Y-m-d\TH:i') : '') }}">
                        <span style="font-size:0.75rem; color:var(--text-muted);">Pode ser corrigida manualmente se a solicitação estiver em 100% de conclusão.</span>
                        @error('data_entrega') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- SALVAR / CANCELAR -->
                <div class="card-section" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="btn-submit-solicitacao-edit">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('solicitacoes.show', $solicitacao->id) }}" class="btn btn-secondary" style="width: 100%;">
                        Cancelar
                    </a>
                </div>

            </div>
        </div>
    </form>

    <script>
        // Contador de caracteres para descrição
        const descTextarea = document.getElementById('descricao');
        const charCounter = document.getElementById('char-counter');
        if (descTextarea && charCounter) {
            const updateCounter = () => {
                const count = descTextarea.value.length;
                charCounter.textContent = `${count} caracteres`;
            };
            descTextarea.addEventListener('input', updateCounter);
            updateCounter();
        }

        // Lógica condicional de exibição da Data de Prazo baseada no Tipo (SAU / SD)
        const tipoSelect = document.getElementById('tipo');
        const dataPrazoInput = document.getElementById('data_prazo');
        if (tipoSelect && dataPrazoInput) {
            const dataPrazoGroup = dataPrazoInput.closest('.form-group');
            const togglePrazoField = () => {
                if (tipoSelect.value === 'SAU') {
                    dataPrazoGroup.style.display = 'none';
                    dataPrazoInput.removeAttribute('required');
                    dataPrazoInput.value = '';
                } else {
                    dataPrazoGroup.style.display = 'block';
                    dataPrazoInput.setAttribute('required', 'required');
                }
            };
            tipoSelect.addEventListener('change', togglePrazoField);
            // Executa na inicialização
            togglePrazoField();
        }
    </script>
@endsection
