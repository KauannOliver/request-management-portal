@extends('layouts.app')

@section('title', 'Quadro Kanban - Gestão SAU & SD')

@section('breadcrumbs')
    <span class="breadcrumbs-item text-secondary">Quadro Kanban</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Quadro Kanban de Solicitações</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Monitore e gerencie o progresso visual das demandas. 
                @if(Auth::user()->isRecurso())
                    <span class="text-primary fw-semibold">Arraste e solte</span> os cartões para transicionar a etapa.
                @endif
            </p>
        </div>
    </div>

    <!-- QUADRO KANBAN CONTAINER -->
    <div class="row g-3 flex-nowrap pb-3" style="overflow-x: auto; min-height: calc(100vh - 220px);">
        @foreach($etapas as $etapa)
            @php
                $cards = isset($solicitacoesPorEtapa[$etapa->id]) ? $solicitacoesPorEtapa[$etapa->id] : collect();
            @endphp
            <div class="col" style="min-width: 300px; max-width: 350px;">
                <div class="card h-100 bg-light border-0 shadow-sm" style="border-radius: var(--radius-md);">
                    <!-- Cabeçalho da coluna -->
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 px-3 pb-2">
                        <h3 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="rounded-circle" style="width: 8px; height: 8px; background-color: var(--blue-primary); display: inline-block;"></span>
                            {{ $etapa->nome }}
                        </h3>
                        <span class="badge bg-secondary text-white rounded-pill px-2 py-1 kanban-column-count" style="font-size: 0.75rem;">
                            {{ $cards->count() }}
                        </span>
                    </div>

                    <!-- Corpo da coluna (Draggable Zone) -->
                    <div class="card-body px-2 py-2 kanban-column-body d-flex flex-column gap-2" 
                         data-etapa-id="{{ $etapa->id }}" 
                         ondragover="allowDrop(event)" 
                         ondrop="drop(event)"
                         style="overflow-y: auto; max-height: calc(100vh - 280px); min-height: 450px;">
                        
                        @forelse($cards as $sol)
                            <div class="card border shadow-sm kanban-card bg-white" 
                                 id="card-{{ $sol->id }}" 
                                 data-id="{{ $sol->id }}" 
                                 draggable="{{ Auth::user()->isRecurso() ? 'true' : 'false' }}" 
                                 ondragstart="drag(event)"
                                 style="cursor: {{ Auth::user()->isRecurso() ? 'grab' : 'default' }}; border-radius: var(--radius-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                
                                <div class="card-body p-3">
                                    <!-- Tag e Prazo -->
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge {{ $sol->tipo === 'SAU' ? 'bg-primary' : 'bg-warning text-dark' }}" style="font-size: 0.7rem; font-weight: 700;">
                                            {{ $sol->tipo }}
                                        </span>
                                        @if($sol->data_prazo)
                                            <span class="text-secondary fw-semibold" style="font-size: 0.7rem;">
                                                Prazo: {{ \Carbon\Carbon::parse($sol->data_prazo)->format('d/m') }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.7rem; font-style: italic;">
                                                Sem prazo
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Título -->
                                    <h4 class="h6 fw-bold mb-2" style="font-size: 0.9rem;">
                                        <a href="{{ route('solicitacoes.show', $sol->id) }}" class="text-decoration-none text-dark hover-primary" style="transition: color 0.2s ease;">
                                            {{ $sol->identificacao }}
                                        </a>
                                    </h4>

                                    <!-- Descrição -->
                                    <p class="text-secondary mb-3" style="font-size: 0.8rem; line-height: 1.4;">
                                        {{ Str::limit($sol->descricao, 80) }}
                                    </p>

                                    <!-- Meta: Progresso e Avatares -->
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                        <!-- Progresso -->
                                        <div class="d-flex align-items-center gap-2 flex-grow-1 me-2">
                                            <div class="progress flex-grow-1" style="height: 5px; background-color: #e9ecef;">
                                                <div class="progress-bar" style="width: {{ $sol->percentual_conclusao }}%; background-color: var(--blue-primary);"></div>
                                            </div>
                                            <span class="text-secondary fw-bold" style="font-size: 0.75rem;">{{ $sol->percentual_conclusao }}%</span>
                                        </div>
                                        
                                        <!-- Avatares -->
                                        <div class="d-flex align-items-center">
                                            @foreach($sol->recursos->take(2) as $index => $rec)
                                                @php $inc = mb_substr($rec->nome, 0, 1); @endphp
                                                <span class="badge bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center border border-white fw-bold" 
                                                      style="width: 24px; height: 24px; font-size: 0.65rem; margin-left: {{ $index > 0 ? '-8px' : '0' }};" 
                                                      title="{{ $rec->nome }}">
                                                    {{ $inc }}
                                                </span>
                                            @endforeach
                                            @if($sol->recursos->count() > 2)
                                                <span class="badge bg-dark text-white rounded-circle d-flex align-items-center justify-content-center border border-white fw-bold" 
                                                      style="width: 24px; height: 24px; font-size: 0.6rem; margin-left: -8px;" 
                                                      title="Mais responsáveis">
                                                    +{{ $sol->recursos->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-secondary italic" style="font-size: 0.8rem; font-style: italic;">
                                Nenhuma demanda nesta etapa.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- CSRF Token e scripts para API de Drag and Drop -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        function allowDrop(ev) {
            ev.preventDefault();
            const col = ev.currentTarget.closest('.card');
            if (col && !col.classList.contains('border-primary')) {
                col.classList.add('border-primary');
                col.style.boxShadow = '0 0 10px rgba(7, 154, 225, 0.25)';
            }
        }

        // Limpar os estilos visuais de hover nas colunas
        document.querySelectorAll('.kanban-column-body').forEach(body => {
            const card = body.closest('.card');
            
            body.addEventListener('dragleave', function() {
                if (card) {
                    card.classList.remove('border-primary');
                    card.style.boxShadow = '';
                }
            });
            body.addEventListener('drop', function() {
                if (card) {
                    card.classList.remove('border-primary');
                    card.style.boxShadow = '';
                }
            });
        });

        function drag(ev) {
            ev.dataTransfer.setData("text/plain", ev.target.dataset.id);
            ev.target.classList.add('opacity-50');
            ev.target.style.cursor = 'grabbing';
        }

        document.addEventListener('dragend', function(ev) {
            if (ev.target.classList.contains('kanban-card')) {
                ev.target.classList.remove('opacity-50');
                ev.target.style.cursor = 'grab';
            }
        });

        function drop(ev) {
            ev.preventDefault();
            
            const targetColBody = ev.currentTarget;
            const targetCard = targetColBody.closest('.card');
            if (targetCard) {
                targetCard.classList.remove('border-primary');
                targetCard.style.boxShadow = '';
            }

            const id = ev.dataTransfer.getData("text/plain");
            const card = document.getElementById("card-" + id);
            
            if (!card) return;

            // Move o card para a nova coluna
            targetColBody.appendChild(card);

            const newEtapaId = targetColBody.dataset.etapaId;

            // Recalcula contadores localmente
            recalcularContadores();

            // Dispara requisição PATCH
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/api/solicitacoes/${id}/etapa`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ etapa_id: newEtapaId })
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Falha ao atualizar etapa');
                }
                return res.json();
            })
            .then(data => {
                showSystemToast("Etapa atualizada com sucesso para a solicitação.", "success");
            })
            .catch(err => {
                console.error(err);
                showSystemToast("Falha ao salvar transição de etapa no servidor.", "error");
                setTimeout(() => window.location.reload(), 1500);
            });
        }

        function recalcularContadores() {
            document.querySelectorAll('.kanban-column-body').forEach(body => {
                const countSpan = body.closest('.card').querySelector('.kanban-column-count');
                const cards = body.querySelectorAll('.kanban-card');
                countSpan.textContent = cards.length;
            });
        }

        function showSystemToast(msg, type) {
            let container = document.querySelector('.toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                container.style.zIndex = '1100';
                document.body.appendChild(container);
            }
            
            const toastId = 'toast-' + Date.now();
            const bgColor = type === 'success' ? '#198754' : '#dc3545';
            const icon = type === 'success' 
                ? '<svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; flex-shrink: 0;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
                : '<svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; flex-shrink: 0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>';
            const title = type === 'success' ? 'Operação Concluída' : 'Ops! Algo deu errado';
            
            const html = `
                <div id="${toastId}" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2 py-3 px-3">
                            ${icon}
                            <div>
                                <strong class="d-block" style="font-size: 0.95rem; font-weight: 700; margin-bottom: 2px;">${title}</strong>
                                <span style="font-size: 0.85rem; opacity: 0.95;">${msg}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
                    </div>
                </div>
            `;
            
            const div = document.createElement('div');
            div.innerHTML = html.trim();
            const toastEl = div.firstChild;
            toastEl.style.backgroundColor = bgColor;
            toastEl.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
            
            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
            toast.show();
            
            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        }
    </script>
@endsection
