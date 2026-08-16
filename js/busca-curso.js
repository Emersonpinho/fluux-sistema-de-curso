// Busca ao vivo dos cursos, sem precisar recarregar a página
document.addEventListener('DOMContentLoaded', () => {

    const campoBusca = document.querySelector('.nav-search input');
    const cards = document.querySelectorAll('.curso-card');
    const grid = document.querySelector('.cursos-grid');

    if (!campoBusca || !grid) return;

    // Mensagem exibida quando nenhum curso bate com a busca
    const semResultados = document.createElement('p');
    semResultados.className = 'sem-resultados';
    semResultados.textContent = 'Nenhum curso encontrado com esse termo.';
    semResultados.style.display = 'none';
    grid.insertAdjacentElement('afterend', semResultados);

    // Palavras que não ajudam a identificar o curso, então são ignoradas na busca
    const palavrasIgnoradas = ['curso', 'cursos', 'de', 'do', 'da', 'em', 'para', 'o', 'a', 'os', 'as', 'sobre'];

    // Remove acentos e deixa tudo minúsculo, pra "ciberseguranca" achar "Cibersegurança"
    function normalizar(texto) {
        return texto
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    campoBusca.addEventListener('input', () => {

        const palavrasBusca = normalizar(campoBusca.value.trim())
            .split(/\s+/)
            .filter(palavra => palavra && !palavrasIgnoradas.includes(palavra));

        let algumVisivel = false;

        cards.forEach(card => {

            const titulo = card.querySelector('h3').textContent;
            const nivel = card.querySelector('.curso-tag')
                ? card.querySelector('.curso-tag').textContent
                : '';

            const textoCard = normalizar(titulo + ' ' + nivel);

            // Só mostra o card se TODAS as palavras digitadas aparecerem em algum lugar do texto
            const corresponde = palavrasBusca.length === 0
                || palavrasBusca.every(palavra => textoCard.includes(palavra));

            card.style.display = corresponde ? '' : 'none';

            if (corresponde) algumVisivel = true;

        });

        semResultados.style.display = algumVisivel ? 'none' : 'block';

    });

});