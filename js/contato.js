/* Envia o formulário de contato via Formspree (formspree.io) */

const form = document.getElementById('form-contato');
const okBox = document.getElementById('form-ok');
const btnEnviar = document.getElementById('btn-enviar-contato');
const btnNovaMsg = document.getElementById('btn-nova-msg');

if (form) {

    // --- CONFIGURAÇÃO FORMSPREE ---
    const FORMSPREE_ID = 'mgawngzr';

    form.addEventListener('submit', e => {
        e.preventDefault();

        const nome = form.querySelector('#nome').value.trim();
        const email = form.querySelector('#email').value.trim();
        const assunto = form.querySelector('#assunto').value;
        const mensagem = form.querySelector('#mensagem').value.trim();

        if (!nome || !email || !mensagem) {
            alert('Por favor, preencha nome, email e mensagem.');
            return;
        }

        // Feedback visual: desabilita o botão enquanto envia
        const textoOriginal = btnEnviar.innerHTML;
        btnEnviar.disabled = true;
        btnEnviar.innerHTML = 'Enviando...';

        const payload = {
            Nome: nome,
            Email: email,
            Assunto: assunto,
            Mensagem: mensagem
        };

        fetch(`https://formspree.io/f/${FORMSPREE_ID}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
            .then(res => {
                if (!res.ok) throw new Error('Erro na resposta do servidor');
                form.style.display = 'none';
                okBox.style.display = 'block';
                okBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            })
            .catch(err => {
                console.error('Erro ao enviar mensagem:', err);
                alert('Ocorreu um erro ao enviar sua mensagem. Tente novamente em instantes.');
            })
            .finally(() => {
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = textoOriginal;
            });
    });

    if (btnNovaMsg) {
        btnNovaMsg.addEventListener('click', () => {
            form.reset();
            form.style.display = 'block';
            okBox.style.display = 'none';
        });
    }
}