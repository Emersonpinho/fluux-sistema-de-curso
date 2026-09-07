# 🎓 Fluux - Sistema de Cursos Online

Uma plataforma educacional moderna e gratuita para disponibilizar cursos de tecnologia para iniciantes e profissionais.

## 📋 Sobre o Projeto

**Fluux** é um sistema de gerenciamento de cursos online desenvolvido com PHP, HTML, CSS e JavaScript. A plataforma oferece uma experiência intuitiva para alunos aprenderem sobre diferentes áreas da tecnologia, desde desenvolvimento web até cibersegurança.

### Características Principais

✨ **Para Alunos:**
- Catálogo de 5 cursos disponíveis
- Autenticação e perfil de usuário
- Sistema de favoritos para salvar cursos
- Busca e filtro de cursos
- Acesso a aulas e conteúdo educacional
- Painel de perfil pessoal

🔒 **Para Administradores:**
- Dashboard com estatísticas em tempo real
- Gerenciamento de cursos (criar, editar, deletar)
- Gerenciamento de alunos e matrículas
- Visualização de aulas
- Painel administrativo exclusivo

## 🎯 Cursos Disponíveis

1. **Desenvolvimento Web** - Iniciante
2. **Desenvolvimento Backend** - Intermediário
3. **Desenvolvimento Mobile** - Iniciante
4. **Estrutura de Dados e Algoritmos** - Avançado
5. **Cibersegurança** - Intermediário

## 🛠️ Tecnologias Utilizadas

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Estilização e design responsivo
- **JavaScript (Vanilla)** - Interatividade e dinâmica

### Backend
- **PHP** - Lógica de negócios e servidor
- **MySQL** - Banco de dados
- **Session Management** - Autenticação de usuários

### Design
- **Poppins & Sora Fonts** - Typography moderna
- **Responsive Design** - Mobile-first approach

## 📁 Estrutura do Projeto

```
fluux-sistema-de-curso/
├── admin/                  # Painel administrativo
│   ├── index.php          # Dashboard principal
│   ├── cursos.php         # Gerenciamento de cursos
│   ├── alunos.php         # Gerenciamento de alunos
│   └── adicionar_curso.php # Criar novo curso
├── pages/                 # Páginas públicas
│   ├── login.php          # Autenticação
│   ├── cadastro.html      # Registro de usuários
│   ├── salvos.html        # Cursos salvos
│   ├── sobre.html         # Sobre o projeto
│   ├── contato.html       # Formulário de contato
│   └── cursos/            # Páginas específicas de cada curso
├── perfil/                # Perfil do usuário
│   └── perfil.php         # Dados pessoais
├── aulas/                 # Conteúdo das aulas
├── assets/                # Imagens e mídia
│   └── images/            # Logos e thumbnails
├── css/                   # Folhas de estilo
│   ├── global.css         # Estilos globais
│   └── admin.css          # Estilos do admin
├── js/                    # Scripts JavaScript
│   ├── busca-curso.js     # Funcionalidade de busca
│   └── favoritos.js       # Sistema de favoritos
├── php/                   # Scripts PHP
│   ├── conexao.php        # Conexão com banco
│   ├── logout.php         # Logout
│   ├── verifica_adm.php   # Verificação de admin
│   └── seed_cursos.php    # Dados iniciais
└── index.php              # Página inicial

```

## 🚀 Como Usar

### Instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/Emersonpinho/fluux-sistema-de-curso.git
   cd fluux-sistema-de-curso
   ```

2. **Configure o servidor web:**
   - Use XAMPP, WAMP, LAMP ou similar
   - Coloque a pasta do projeto na pasta `htdocs` (ou equivalente)

3. **Configure o banco de dados:**
   - Crie um banco de dados MySQL
   - Configure as credenciais em `php/conexao.php`
   - Execute os scripts de inicialização

4. **Acesse a aplicação:**
   ```
   http://localhost/fluux-sistema-de-curso
   ```

### Primeiro Acesso

- **Como Aluno:** Clique em "Cadastrar" para criar uma nova conta
- **Como Admin:** Acesse com credenciais administrativas em `http://localhost/fluux-sistema-de-curso/admin`

## 👥 Usuários

### Tipos de Usuários

- **Aluno:** Acesso ao catálogo de cursos, perfil pessoal e salvos
- **Administrador (ADM):** Acesso completo ao dashboard, gerenciamento de cursos e alunos

## 📊 Dashboard Administrativo

O painel de administração oferece:

- **Estatísticas em Tempo Real:**
  - Total de alunos cadastrados
  - Total de cursos disponíveis
  - Total de aulas
  - Matrículas ativas

- **Gerenciamento:**
  - Lista de todos os cursos com status
  - Últimos alunos cadastrados
  - Ações rápidas para criar novos cursos

## 🎨 Design e UX

- **Paleta de Cores:** Azul escuro (#2857c6), Verde neon (#c6ff5c), Branco
- **Typography:** Poppins (corpo), Sora (títulos)
- **Layout Responsivo:** Funciona em desktop, tablet e mobile
- **Acessibilidade:** Semântica HTML apropriada e ARIA labels

## 🔐 Segurança

- Validação de entrada em formulários PHP
- Proteção contra SQL Injection (prepared statements)
- Sessões seguras para autenticação
- Escape de dados com `htmlspecialchars()`
- Verificação de permissões administrativas

## 📝 Funcionalidades Principais

### Sistema de Autenticação
- Registro de novos usuários
- Login seguro com sessões
- Logout e destruição de sessão
- Verificação de acesso administrativo

### Gerenciamento de Cursos
- Cadastro de cursos com metadados
- Sistema de níveis (Iniciante, Intermediário, Avançado)
- Status de atividade dos cursos
- Duração e informações de carga horária

### Sistema de Favoritos
- Salvar cursos para acesso rápido
- Persistência de dados em localStorage
- Interface visual de favoritos

### Busca de Cursos
- Busca em tempo real
- Filtro por nome e características
- Sugestões inteligentes

## 🛣️ Roadmap

- [ ] Sistema de progresso de aulas
- [ ] Certificados digitais
- [ ] Comentários e avaliações
- [ ] Fórum de discussão
- [ ] Integração com pagamentos (Stripe/PayPal)
- [ ] Sistema de notificações por email
- [ ] API REST para mobile
- [ ] Testes automatizados

## 📱 Compatibilidade

- ✅ Chrome (versões recentes)
- ✅ Firefox (versões recentes)
- ✅ Safari (versões recentes)
- ✅ Edge (versões recentes)
- ✅ Mobile browsers (iOS e Android)

## 🐛 Contribuindo

Encontrou um bug ou tem uma sugestão? Abra uma [issue](https://github.com/Emersonpinho/fluux-sistema-de-curso/issues)!

Para contribuir com código:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a MIT License - veja o arquivo LICENSE para mais detalhes.

## 👨‍💻 Autor

**Emerson Pinho**
- GitHub: [@Emersonpinho](https://github.com/Emersonpinho)

## 📞 Suporte

Se tiver dúvidas ou precisar de ajuda, entre em contato através do formulário de contato no site ou abra uma issue no repositório.

## 🙏 Agradecimentos

- Google Fonts por fonts incríveis
- Comunidade de desenvolvedores
- Todos os contribuidores

---

**Feito com ❤️ para a comunidade educacional tech**
