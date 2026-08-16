// Gerenciador de Cursos Salvos (PHP + LocalStorage Fallback)
document.addEventListener('DOMContentLoaded', () => {

    // Base de dados dos cursos para renderização dinâmica na página de salvos
    const BANCO_CURSOS = {
        '001': {
            codigo: '001',
            titulo: 'Desenvolvimento Web',
            tag: 'Iniciante',
            imagem: 'assets/images/cursos/web.webp',
            link: 'pages/cursos/curso-web.html'
        },
        '002': {
            codigo: '002',
            titulo: 'Desenvolvimento Backend',
            tag: 'Intermediário',
            imagem: 'assets/images/cursos/backend.webp',
            link: 'pages/cursos/curso-backend.html'
        },
        '003': {
            codigo: '003',
            titulo: 'Desenvolvimento Mobile',
            tag: 'Iniciante',
            imagem: 'assets/images/cursos/mobile.jpg',
            link: 'pages/cursos/curso-mobile.html'
        },
        '004': {
            codigo: '004',
            titulo: 'Estrutura de Dados e Algoritmos',
            tag: 'Avançado',
            imagem: 'assets/images/cursos/algoritmo.jpg',
            link: 'pages/cursos/curso-estrutura-dados.html'
        },
        '005': {
            codigo: '005',
            titulo: 'Cibersegurança',
            tag: 'Intermediário',
            imagem: 'assets/images/cursos/ciberseguranca.webp',
            link: 'pages/cursos/curso-ciberseguranca.html'
        }
    };

    // Determina caminhos relativos de acordo com a pasta da página atual
    const isInsidePages = window.location.pathname.includes('/pages/');
    const isInsideCursosOuAulas = window.location.pathname.includes('/pages/cursos/') || window.location.pathname.includes('/aulas/');

    function getPhpEndpoint() {
        if (isInsideCursosOuAulas) return '../../php/favoritar.php';
        if (isInsidePages) return '../php/favoritar.php';
        return 'php/favoritar.php';
    }

    function getRelativePath(pathStr) {
        if (isInsideCursosOuAulas) return '../../' + pathStr;
        if (isInsidePages) return '../' + pathStr;
        return pathStr;
    }

    // LocalStorage helper para funcionamento 100% garantido mesmo em servidores estáticos
    function getLocalSalvos() {
        try {
            const data = localStorage.getItem('fluux_salvos');
            return data ? JSON.parse(data) : [];
        } catch (e) {
            return [];
        }
    }

    function setLocalSalvos(arr) {
        try {
            localStorage.setItem('fluux_salvos', JSON.stringify(arr));
        } catch (e) {}
    }

    function toggleLocalSalvo(codigo) {
        let salvos = getLocalSalvos();
        const pos = salvos.indexOf(codigo);
        if (pos !== -1) {
            salvos.splice(pos, 1);
            setLocalSalvos(salvos);
            return false;
        } else {
            salvos.push(codigo);
            setLocalSalvos(salvos);
            return true;
        }
    }

    // Elemento Toast para notificações flutuantes
    let toast = document.querySelector('.toast-notificacao');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast-notificacao';
        document.body.appendChild(toast);
    }

    let timerToast = null;
    function exibirToast(mensagem, salvo) {
        if (!toast) return;
        toast.innerHTML = (salvo ? '🔖 ' : 'ℹ️ ') + mensagem;
        toast.classList.add('visivel');

        if (timerToast) clearTimeout(timerToast);
        timerToast = setTimeout(() => {
            toast.classList.remove('visivel');
        }, 2800);
    }

    // Atualiza o estado visual dos botões
    function atualizarVisualBotao(btn, salvo) {
        if (!btn) return;
        btn.classList.toggle('favoritado', salvo);
        btn.classList.toggle('salvo', salvo);
        btn.setAttribute('aria-label', salvo ? 'Remover dos salvos' : 'Salvar curso');
        btn.setAttribute('title', salvo ? 'Remover dos salvos' : 'Salvar curso');

        const svgPath = btn.querySelector('svg path');
        const svg = btn.querySelector('svg');
        if (svg) {
            if (salvo) {
                svg.setAttribute('fill', 'currentColor');
            } else {
                svg.setAttribute('fill', 'none');
            }
        }
    }

    function aplicarEstadoSalvoEmTodos(codigo, salvo) {
        const btns = document.querySelectorAll(`.btn-favorito[data-codigo="${codigo}"], .btn-salvar[data-codigo="${codigo}"]`);
        btns.forEach(btn => atualizarVisualBotao(btn, salvo));
    }

    // Renderiza dinamicamente os cursos salvos se estivermos na página salvos.html
    function renderizarPaginaSalvos(codigosSalvos) {
        const gridSalvos = document.getElementById('grid-salvos');
        const emptyState = document.getElementById('empty-salvos');

        if (!gridSalvos || !emptyState) return;

        gridSalvos.innerHTML = '';

        const salvosValidos = codigosSalvos.filter(codigo => BANCO_CURSOS[codigo]);

        if (salvosValidos.length === 0) {
            gridSalvos.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        gridSalvos.style.display = 'grid';
        emptyState.style.display = 'none';

        salvosValidos.forEach(codigo => {
            const curso = BANCO_CURSOS[codigo];
            const imgPath = getRelativePath(curso.imagem);
            const linkPath = getRelativePath(curso.link);

            const article = document.createElement('article');
            article.className = 'curso-card';
            article.setAttribute('data-card-codigo', codigo);
            article.innerHTML = `
                <div class="curso-thumb">
                    <img src="${imgPath}" alt="${curso.titulo}">
                    <span class="curso-tag">${curso.tag}</span>
                </div>
                <div class="curso-info">
                    <span class="curso-codigo">Código ${curso.codigo}</span>
                    <h3>${curso.titulo}</h3>
                    <span class="curso-carga">Em andamento</span>
                    <div class="curso-acoes">
                        <a class="btn-detalhes" href="${linkPath}">Realizar curso</a>
                        <button class="btn-salvar btn-favorito salvo favoritado" data-codigo="${codigo}" aria-label="Remover dos salvos" title="Remover dos salvos">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                        </button>
                    </div>
                </div>
            `;
            gridSalvos.appendChild(article);
        });
    }

    // Inicialização: carrega salvos via PHP ou fallback do LocalStorage
    const endpoint = getPhpEndpoint();
    const salvosLocais = getLocalSalvos();

    fetch(endpoint)
        .then(res => res.json())
        .then(data => {
            let listaFinal = [];
            if (data.success && Array.isArray(data.favoritos)) {
                listaFinal = data.favoritos;
                setLocalSalvos(listaFinal);
            } else {
                listaFinal = salvosLocais;
            }

            listaFinal.forEach(codigo => aplicarEstadoSalvoEmTodos(codigo, true));

            if (window.location.pathname.includes('salvos.html')) {
                renderizarPaginaSalvos(listaFinal);
            }
        })
        .catch(() => {
            // Em servidores sem suporte a PHP (como Live Server 5500), usa LocalStorage
            salvosLocais.forEach(codigo => aplicarEstadoSalvoEmTodos(codigo, true));
            if (window.location.pathname.includes('salvos.html')) {
                renderizarPaginaSalvos(salvosLocais);
            }
        });

    // Evento de clique para salvar/desfavoritar
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-favorito, .btn-salvar');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const codigo = btn.getAttribute('data-codigo');
        if (!codigo) return;

        const estadoAtual = btn.classList.contains('favoritado') || btn.classList.contains('salvo');
        const novoEstado = !estadoAtual;

        // Atualiza imediatamente o LocalStorage e o visual
        toggleLocalSalvo(codigo);
        aplicarEstadoSalvoEmTodos(codigo, novoEstado);

        // Se estivermos na página salvos.html e desfavoritou, anima e remove o card
        const cardSalvamento = btn.closest('[data-card-codigo]');
        if (cardSalvamento && !novoEstado && window.location.pathname.includes('salvos.html')) {
            cardSalvamento.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            cardSalvamento.style.opacity = '0';
            cardSalvamento.style.transform = 'scale(0.9)';
            setTimeout(() => {
                cardSalvamento.remove();
                const cardsRestantes = document.querySelectorAll('[data-card-codigo]');
                if (cardsRestantes.length === 0) {
                    const emptyState = document.getElementById('empty-salvos');
                    const gridSalvos = document.getElementById('grid-salvos');
                    if (gridSalvos) gridSalvos.style.display = 'none';
                    if (emptyState) emptyState.style.display = 'block';
                }
            }, 300);
        }

        // Notifica o servidor PHP se disponível
        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                aplicarEstadoSalvoEmTodos(codigo, data.favoritado);
                exibirToast(data.favoritado ? 'Curso salvo nos seus salvos!' : 'Curso removido dos salvos!', data.favoritado);
            } else {
                exibirToast(novoEstado ? 'Curso salvo nos seus salvos!' : 'Curso removido dos salvos!', novoEstado);
            }
        })
        .catch(() => {
            exibirToast(novoEstado ? 'Curso salvo nos seus salvos!' : 'Curso removido dos salvos!', novoEstado);
        });
    });

});
