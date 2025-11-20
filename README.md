# 📰 Ponto Esportivo - Portal de Notícias Esportivas

## 🎯 Descrição do Projeto
Portal de notícias esportivas desenvolvido em PHP com sistema completo de autenticação, CRUD de notícias e usuários, e interface responsiva. 
Esse projeto, foi uma aplicação em PHP para o projeto final da matéria, com o professor Jefferson Leon da escola Ulbra São Lucas. 

## 🏗️ Estrutura do Projeto

ProjetoFinalWebII/

│

├── 📁 config/

│ └── config.php # Configurações do banco de dados

│

├── 📁 classes/

│ ├── Usuario.php # Classe para operações de usuário

│ └── Noticia.php # Classe para operações de notícia

│ └── Database.php # Classe para Banco de Dados                                              

│

├── 📁 css/

│ ├── index.css # Estilos da página inicial

│ ├── meu_painel.css # Estilos do painel do usuário

│ ├── noticia.css # Estilos da página de notícia individual

│ ├── admin_usuarios.css # Estilos do painel administrativo

│ ├── nova_noticia.css # Estilos do formulário de nova notícia

| ├── editar_noticia.css # Estilos do formulário de edição de notícia

| ├── editar_usuario_admin.css # Estilos do formulário de edição de usuário do administrador

| ├── editar_perfil.css # Estilos do formulário de edição de perfil do usuário

| ├── login.css # Estilos do formulário de Login

│ └── registrar.css # Estilos do formulário de registro

│

├── 📁 imagens/

│ └── noticias/ - Pasta para upload de imagens das notícias

│

├── 📄 index.php - Página inicial com listagem de notícias

├── 📄 noticias.php - Página individual da notícia

├── 📄 login.php - Formulário de login

├── 📄 registrar.php - Formulário de registro de usuários

├── 📄 logout.php - Encerramento de sessão

│

├── 📄 meu_painel.php - Painel do usuário logado

├── 📄 nova_noticia.php - Formulário para criar nova notícia

├── 📄 editar_noticia.php - Edição de notícias

│

├── 📄 admin_usuarios.php - Painel administrativo (gerenciar usuários)

├── 📄 editar_usuario_admin.php - Edição de usuários (admin)

│

└── 📄 dbportalesportes.sql - Estrutura do banco de dados



⚙️ Funcionalidades Implementadas

🔐 Sistema de Autenticação

✅ Cadastro de novos usuários pelo Administrador

✅ Login com verificação de credenciais

✅ Logout seguro

✅ Sessões PHP para controle de acesso

📰 Gestão de Notícias

✅ CRUD Completo: Criar, Ler, Editar e Excluir notícias (admin)

✅ Upload de imagens para notícias

✅ Validação de dados (título, conteúdo, imagem)

✅ Associação automática com autor logado

✅ Listagem pública na página inicial

👤 Gestão de Usuários

✅ Perfil de usuário

✅ Painel administrativo (apenas para admin)

✅ Busca de usuários por nome

✅ Exclusão de usuários (somente admin)

🎨 Interface e UX

✅ Design responsivo

✅ Widget de clima em tempo real (API Open-Meteo)

✅ Navegação intuitiva

✅ Validação de formulários

🚀 Como Executar o Projeto
1. Requisitos
Servidor web (Apache/XAMPP)

PHP 7.4+

MySQL/MariaDB

2. Configuração
Colocar os arquivos na pasta htdocs do XAMPP

Importar o arquivo dump.sql no phpMyAdmin

Configurar conexão no arquivo config/config.php

Acessar via: http://localhost/ProjetoFinalWebII/

3. Usuário Admin Padrão
ID: 1 (primeiro usuário cadastrado automaticamente se não existir)

Privilégios: Acesso ao painel administrativo

🔧 Tecnologias Utilizadas
Backend: PHP 7.4+, MySQL

Frontend: HTML5, CSS3, JavaScript

APIs: Open-Meteo (clima)

Segurança: Prepared Statements, Password Hash, Session Management

🛡️ Recursos de Segurança
✅ Prepared Statements contra SQL Injection

✅ Hash de senhas com password_hash()

✅ Validação de upload de imagens

✅ Verificação de sessões

✅ Controle de permissões

✅ Sanitização de dados de entrada

📱 Responsividade
O sistema é totalmente responsivo e funciona em:

✅ Desktop

✅ Tablet

✅ Mobile

🌟 Funcionalidades Especiais
Widget de Clima
Exibe temperatura atual de Sapucaia do Sul/RS

Ícones dinâmicos baseados nas condições climáticas

Atualização em tempo real via API

Sistema de Busca
Busca de usuários por nome (painel admin)

Filtragem em tempo real

Painel Administrativo
Estatísticas de usuários

Gerenciamento completo de usuários

Interface dedicada para administradores

👥 Permissões e Acessos

👥Usuário Comum

Editar próprio perfil

Visualizar notícias públicas

👥Administrador 

Todas as permissões de usuário comum

Gerenciar todos os usuários

Acesso ao painel administrativo

## 📞 Contato

**Desenvolvido por:** Bruno Model Martinho  
**Email:** brunomodel60@gmail.com
**GitHub:** https://github.com/Bruno7Martinho

*Sistema desenvolvido como Trabalho Final da disciplina de Desenvolvimento Web II.*  
*Ulbra São Lucas, Técnico em Informática - Sapucaia do Sul*  
*Curso: Técnico em Informática - [Terceiro Semestre]*
