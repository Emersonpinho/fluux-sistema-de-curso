// Gerenciador de Matrículas (tabela matricula_curso)
document.addEventListener('DOMContentLoaded', () => {

    const isInsidePages = window.location.pathname.includes('/pages/') || window.location.pathname.includes('/perfil/');
    const isInsideCursosOuAulas = window.location.pathname.includes('/pages/cursos/') || window.location.pathname.includes('/aulas/');

    function getMatriculaEndpoint() {
        if (isInsideCursosOuAulas) return '../../php/matricular.php';
        if (isInsidePages) return '../php/matricular.php';
        return 'php/matricular.php';
    }

    function getLoginPath() {
        if (isInsideCursosOuAulas) return '../login.php';
        if (isInsidePages) return 'login.php';
        return 'pages/login.php';
    }

    // Toast de notificação
    let toast = document.querySelector('.toast-notificacao');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast-notificacao';
        document.body.appendChild(toast);
    }

    let timerToast = null;
    function exibirToast(mensagem, sucesso) {
        if (!toast) return;
        toast.innerHTML = (sucesso ? '🎓 ' : 'ℹ️ ') + mensagem;
        toast.classList.add('visivel');

        if (timerToast) clearTimeout(timerToast);
        timerToast = setTimeout(() => {
            toast.classList.remove('visivel');
        }, 3200);
    }

    const endpoint = getMatriculaEndpoint();

    // Consulta matrículas ativas ao carregar a página
    fetch(endpoint)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.logado && Array.isArray(data.matriculas)) {
                data.matriculas.forEach(codigo => {
                    atualizarVisualMatricula(codigo, true);
                });
            }
        })
        .catch(() => {});

    function atualizarVisualMatricula(codigo, matriculado) {
        const codNorm = String(codigo).padStart(3, '0');
        const codInt = String(parseInt(codigo, 10));

        const btns = document.querySelectorAll(
            `.btn-matricular[data-codigo="${codigo}"], .btn-matricular[data-codigo="${codNorm}"], .btn-matricular[data-codigo="${codInt}"]`
        );

        btns.forEach(btn => {
            if (matriculado) {
                btn.classList.add('matriculado');
                btn.innerHTML = '<span>✓</span> Matriculado';
                btn.setAttribute('title', 'Você já está matriculado neste curso!');
            } else {
                btn.classList.remove('matriculado');
                btn.innerHTML = 'Matricular-se';
                btn.removeAttribute('title');
            }
        });
    }

    // Clique no botão de Matricular-se
    document.addEventListener('click', (e) => {
        const btnMatricula = e.target.closest('.btn-matricular');
        if (btnMatricula) {
            e.preventDefault();

            const codigo = btnMatricula.getAttribute('data-codigo');
            if (!codigo) return;

            // Se já está matriculado, informa o aluno
            if (btnMatricula.classList.contains('matriculado')) {
                exibirToast('Você já está matriculado neste curso! Bons estudos.', true);
                return;
            }

            btnMatricula.disabled = true;
            const textoOriginal = btnMatricula.innerHTML;
            btnMatricula.innerHTML = 'Matriculando...';

            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ codigo: codigo, acao: 'matricular' })
            })
            .then(res => res.json())
            .then(data => {
                btnMatricula.disabled = false;
                if (data.success && data.matriculado) {
                    atualizarVisualMatricula(codigo, true);
                    exibirToast(data.mensagem || 'Matrícula realizada com sucesso!', true);
                } else if (!data.logado) {
                    exibirToast(data.message || 'Faça login para se matricular.', false);
                    setTimeout(() => {
                        window.location.href = getLoginPath();
                    }, 1400);
                } else {
                    btnMatricula.innerHTML = textoOriginal;
                    exibirToast(data.message || data.mensagem || 'Não foi possível matricular.', false);
                }
            })
            .catch(() => {
                btnMatricula.disabled = false;
                btnMatricula.innerHTML = textoOriginal;
                exibirToast('Erro ao conectar com o servidor.', false);
            });
            return;
        }

        // Clique para Cancelar Matrícula (dentro do Perfil)
        const btnCancelar = e.target.closest('.btn-cancelar-matricula');
        if (btnCancelar) {
            e.preventDefault();

            const codigo = btnCancelar.getAttribute('data-codigo');
            if (!codigo) return;

            if (!confirm('Deseja realmente cancelar a sua matrícula neste curso?')) {
                return;
            }

            btnCancelar.disabled = true;

            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ codigo: codigo, acao: 'cancelar' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    exibirToast(data.mensagem || 'Matrícula cancelada.', true);

                    const card = btnCancelar.closest('[data-card-matricula]');
                    if (card) {
                        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();

                            const cardsRestantes = document.querySelectorAll('.matriculas-grid-perfil [data-card-matricula]');
                            const badge = document.querySelector('.badge-contagem-matriculas');
                            if (badge) {
                                badge.textContent = `${cardsRestantes.length} curso(s)`;
                            }
                            if (cardsRestantes.length === 0) {
                                const grid = document.querySelector('.matriculas-grid-perfil');
                                const empty = document.querySelector('.empty-matriculas-perfil');
                                if (grid) grid.style.display = 'none';
                                if (empty) empty.style.display = 'block';
                            }
                        }, 300);
                    }
                } else {
                    btnCancelar.disabled = false;
                    exibirToast(data.message || 'Erro ao cancelar matrícula.', false);
                }
            })
            .catch(() => {
                btnCancelar.disabled = false;
                exibirToast('Erro de comunicação.', false);
            });
        }
    });

});
