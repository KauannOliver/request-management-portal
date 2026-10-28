<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <!-- Toast de Sucesso -->
    @if(session('success'))
        <div id="toast-success" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: var(--blue-primary); box-shadow: 0 8px 30px rgba(33,42,87,0.15);">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 py-3 px-3">
                    <!-- Ícone Sucesso -->
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; flex-shrink: 0;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    <div>
                        <strong class="d-block" style="font-size: 0.95rem; font-weight: 700; margin-bottom: 2px;">Operação Concluída</strong>
                        <span style="font-size: 0.85rem; opacity: 0.95;">{{ session('success') }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
        </div>
    @endif

    <!-- Toast de Erro -->
    @if(session('error') || $errors->any())
        <div id="toast-error" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: var(--orange-alert); box-shadow: 0 8px 30px rgba(33,42,87,0.15);">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 py-3 px-3">
                    <!-- Ícone Erro -->
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; flex-shrink: 0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <div>
                        <strong class="d-block" style="font-size: 0.95rem; font-weight: 700; margin-bottom: 2px;">Ops! Algo deu errado</strong>
                        <span style="font-size: 0.85rem; opacity: 0.95;">
                            @if(session('error'))
                                {{ session('error') }}
                            @else
                                Existem erros de preenchimento. Por favor, verifique.
                            @endif
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
        </div>
    @endif
</div>

@if(session('success') || session('error') || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastElList = [].slice.call(document.querySelectorAll('.toast'));
        toastElList.map(function(toastEl) {
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        });
    });
</script>
@endif
