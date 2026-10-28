// Gestão SAU & SD - Componentes de Interface Dinâmicos

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. Controle da Sidebar e Responsividade
    // ----------------------------------------------------
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const appContainer = document.querySelector('.app-container');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (window.innerWidth > 1024) {
                sidebar.classList.toggle('collapsed');
                appContainer.classList.toggle('sidebar-collapsed-active');
            } else {
                sidebar.classList.toggle('mobile-open');
            }
        });
    }

    // Fechar menu mobile ao clicar fora
    document.addEventListener('click', (e) => {
        if (sidebar && sidebar.classList.contains('mobile-open') && !sidebar.contains(e.target) && e.target !== toggleBtn) {
            sidebar.classList.remove('mobile-open');
        }
    });

    // ----------------------------------------------------
    // 2. Toasts Auto-close
    // ----------------------------------------------------
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });

    // ----------------------------------------------------
    // 3. Controle Deslizante de Progresso (Custom Slider)
    // ----------------------------------------------------
    const rangeSlider = document.getElementById('percentual_conclusao_slider');
    const hiddenPercentInput = document.getElementById('percentual_conclusao');
    const valDisplay = document.getElementById('slider-val-display');
    const descDisplay = document.getElementById('slider-desc-display');
    const dataEntregaContainer = document.getElementById('data-entrega-container');

    if (rangeSlider && hiddenPercentInput && valDisplay && descDisplay) {
        // Valor original para verificação de reabertura (edit)
        const valorOriginal = parseInt(hiddenPercentInput.value) || 0;

        const obterMensagem = (val) => {
            if (val === 0) return 'Planejamento e inicialização.';
            if (val > 0 && val <= 25) return 'Levantamento e definição do escopo inicial.';
            if (val > 25 && val <= 50) return 'Desenvolvimento e execução em andamento.';
            if (val > 50 && val <= 75) return 'Refinamentos de código e arquitetura finalizados.';
            if (val > 75 && val < 100) return 'Homologação e testes finais com usuários.';
            return 'Conclusão e implantação no ambiente de produção!';
        };

        const atualizarSliderInterface = (val) => {
            hiddenPercentInput.value = val;
            valDisplay.textContent = `${val}%`;
            descDisplay.textContent = obterMensagem(val);

            // Exibir/Ocultar campo de data de entrega se for 100%
            if (dataEntregaContainer) {
                if (val === 100) {
                    dataEntregaContainer.style.display = 'block';
                } else {
                    dataEntregaContainer.style.display = 'none';
                }
            }
        };

        rangeSlider.addEventListener('input', (e) => {
            const novoValor = parseInt(e.target.value);
            
            // Confirmar reabertura se voltar de 100% para menos
            if (valorOriginal === 100 && novoValor < 100) {
                if (!confirm('Atenção: Ao reduzir o progresso para menos de 100%, a data de entrega será removida e a solicitação será considerada reaberta. Deseja continuar?')) {
                    rangeSlider.value = 100;
                    atualizarSliderInterface(100);
                    return;
                }
            }
            atualizarSliderInterface(novoValor);
        });

        // Suporte a cliques nos ticks
        const ticks = document.querySelectorAll('.slider-tick');
        ticks.forEach(tick => {
            tick.addEventListener('click', () => {
                const tickVal = parseInt(tick.getAttribute('data-value'));
                if (valorOriginal === 100 && tickVal < 100) {
                    if (!confirm('Atenção: Ao reduzir o progresso para menos de 100%, a data de entrega será removida e a solicitação será considerada reaberta. Deseja continuar?')) {
                        return;
                    }
                }
                rangeSlider.value = tickVal;
                atualizarSliderInterface(tickVal);
            });
        });
    }

    // ----------------------------------------------------
    // 4. Seletor de Recursos Moderno (Multiselect com chips e busca)
    // ----------------------------------------------------
    const selectTrigger = document.getElementById('recursos-trigger');
    const selectDropdown = document.getElementById('recursos-dropdown');
    const searchInput = document.getElementById('recursos-search');
    const chipsContainer = document.getElementById('recursos-chips-container');
    const checkboxes = document.querySelectorAll('.recurso-checkbox');

    if (selectTrigger && selectDropdown && chipsContainer) {
        // Toggle dropdown
        selectTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            selectDropdown.classList.toggle('open');
            if (searchInput) searchInput.focus();
        });

        document.addEventListener('click', (e) => {
            if (!selectDropdown.contains(e.target) && !selectTrigger.contains(e.target)) {
                selectDropdown.classList.remove('open');
            }
        });

        // Filtrar opções
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const termo = e.target.value.toLowerCase();
                const options = selectDropdown.querySelectorAll('.multiselect-option');
                options.forEach(opt => {
                    const nome = opt.getAttribute('data-nome').toLowerCase();
                    const email = opt.getAttribute('data-email').toLowerCase();
                    if (nome.includes(termo) || email.includes(termo)) {
                        opt.style.display = 'flex';
                    } else {
                        opt.style.display = 'none';
                    }
                });
            });
        }

        // Atualizar Chips na interface
        const renderizarChips = () => {
            chipsContainer.innerHTML = '';
            let selecionados = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    selecionados++;
                    const id = cb.value;
                    const nome = cb.getAttribute('data-nome');
                    const inicial = nome.charAt(0).toUpperCase();

                    const chip = document.createElement('div');
                    chip.className = 'chip';
                    chip.innerHTML = `
                        <span class="chip-avatar">${inicial}</span>
                        <span>${nome}</span>
                        <svg class="remove-chip" data-id="${id}" style="width:12px; height:12px; cursor:pointer;" viewBox="0 0 24 24"><path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                    `;
                    chipsContainer.appendChild(chip);
                }
            });

            // Adiciona trigger para remover chip
            const removeBtns = chipsContainer.querySelectorAll('.remove-chip');
            removeBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = btn.getAttribute('data-id');
                    const matchingCb = document.querySelector(`.recurso-checkbox[value="${id}"]`);
                    if (matchingCb) {
                        matchingCb.checked = false;
                        renderizarChips();
                    }
                });
            });

            if (selecionados === 0) {
                chipsContainer.innerHTML = '<span style="color:var(--text-muted); font-size:0.9rem;">Selecione os responsáveis...</span>';
            }
        };

        // Escutar alterações nos checkboxes
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                renderizarChips();
            });
        });

        // Renderizar inicial
        renderizarChips();
    }

    // ----------------------------------------------------
    // 5. Confirmações de Alterações Não Salvas
    // ----------------------------------------------------
    const formEdicao = document.querySelector('.form-confirm-leave');
    if (formEdicao) {
        let formAlterado = false;
        formEdicao.addEventListener('change', () => {
            formAlterado = true;
        });

        // Ignora aviso ao submeter
        formEdicao.addEventListener('submit', () => {
            formAlterado = false;
        });

        window.addEventListener('beforeunload', (e) => {
            if (formAlterado) {
                e.preventDefault();
                e.returnValue = 'Você possui alterações não salvas. Deseja realmente sair?';
            }
        });
    }

    // ----------------------------------------------------
    // 6. Alerta de Retrocesso de Etapa
    // ----------------------------------------------------
    const etapaSelect = document.getElementById('etapa_id');
    if (etapaSelect && etapaSelect.dataset.warnRetrocesso === 'true') {
        let etapaOriginal = etapaSelect.value;

        etapaSelect.addEventListener('change', (e) => {
            const etapaNova = e.target.value;
            // Para descobrir se retrocedeu, olhamos a ordem no dropdown
            const optOriginal = etapaSelect.querySelector(`option[value="${etapaOriginal}"]`);
            const optNova = etapaSelect.querySelector(`option[value="${etapaNova}"]`);
            
            if (optOriginal && optNova) {
                const ordemOriginal = parseInt(optOriginal.getAttribute('data-ordem'));
                const ordemNova = parseInt(optNova.getAttribute('data-ordem'));

                if (ordemNova < ordemOriginal) {
                    if (!confirm('Atenção: Você está retrocedendo a etapa desta solicitação. Deseja confirmar esta ação? Ela será auditada.')) {
                        etapaSelect.value = etapaOriginal;
                        return;
                    }
                }
            }
            etapaOriginal = etapaNova;
        });
    }
});

// Funções Auxiliares de Modais Globais
window.abrirModalExcluir = (id, identificacao) => {
    const modal = document.getElementById('modal-excluir-solicitacao');
    const form = document.getElementById('form-excluir-solicitacao');
    const titleSpan = document.getElementById('excluir-identificacao-span');

    if (modal && form && titleSpan) {
        titleSpan.textContent = identificacao;
        form.action = `/solicitacoes/excluir/${id}`;
        modal.classList.add('open');
    }
};

window.fecharModalExcluir = () => {
    const modal = document.getElementById('modal-excluir-solicitacao');
    if (modal) {
        modal.classList.remove('open');
    }
};
