# 🔐 Sistema de Autenticação Segura (Firebase + PHP)

![Status](https://img.shields.io/badge/Status-Ativo-success)
![Stack](https://img.shields.io/badge/Stack-PHP%20%7C%20Vanilla%20JS%20%7C%20Firebase-blue)
![License](https://img.shields.io/badge/License-MIT-lightgrey)

> Um sistema de autenticação híbrido utilizando **Firebase Auth (Google & Email)** no frontend e validação de tokens JWT via **API REST com PHP** no backend.

---

## 🎯 O Diferencial Técnico deste Projeto

Diferente de implementações simples de Firebase, a arquitetura deste projeto foi pensada para simular um ambiente real de produção, focando em **Segurança e Experiência do Usuário (UX)**:

- **Validação Server-Side (JWT):** O token gerado pelo Firebase no frontend não é aceito cegamente. Ele é enviado ao backend (`verificar.php`), que consome a API REST do Google para garantir a integridade do token antes de liberar a sessão PHP.
- **Tratador Customizado de Erros:** Criação de um "escudo" no frontend que intercepta códigos de erro técnicos do Firebase (como `auth/wrong-password`) e os traduz para mensagens amigáveis e claras para o usuário final, elevando o nível da UX.
- **Proteção de Rotas:** Bloqueio estrito de páginas internas (`dashboard.php`), acessíveis apenas mediante validação de sessão ativa no servidor, e não apenas por verificações visuais no cliente.

---

## 🛠 Tecnologias Utilizadas

- **Backend:** PHP 8+ (Gerenciamento de Sessão, cURL, REST API)
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla ES6+)
- **BaaS (Backend as a Service):** Firebase Authentication, SDK Web V9 (Compat)
- **Protocolos:** OAuth 2.0 (Google Login), JWT (JSON Web Tokens)

---

## 🚀 Funcionalidades

- Interface de Login minimalista e responsiva.
- Autenticação por E-mail/Senha e Login Social (Google).
- Controle de sessão no frontend e validação simultânea no backend.
- Painel de Dashboard dinâmico com cálculo de tempo de sessão em tempo real.
- Logout seguro com destruição de sessão HTTP.

---

## ▶️ Como Executar o Projeto Localmente

1. Clone este repositório: `git clone https://github.com/codebyaires/sistema-autenticacao-firebase.git`
2. Configure um servidor local (XAMPP, WAMP, Laragon, etc.) e coloque a pasta do projeto no diretório raiz (`htdocs` ou `www`).
3. Acesse o [Console do Firebase](https://console.firebase.google.com/), crie um projeto e ative os métodos de login com Email/Senha e Google em **Authentication**.
4. Copie as suas credenciais do Firebase (API Key, Project ID, etc.).
5. No projeto, atualize as credenciais nos arquivos `app.js` e `verificar.php`.
6. Execute o projeto acessando `http://localhost/nome-da-pasta` no seu navegador.

---

## 👨‍💻 Desenvolvimento e Autoria

Projeto arquitetado e desenvolvido com foco em boas práticas de código por:

* **Victor Gabriel** - [LinkedIn](www.linkedin.com/in/victor-aires-93621636a) | [GitHub](https://github.com/codebyaires)

* **Peterson Ruivo** - [LinkedIn](https://www.linkedin.com/in/petersonruivo/) |[GitHub](https://github.com/ruivocodespace)

* **Vitor Augusto** - [LinkedIn](https://www.linkedin.com/in/vitor-a-lucn/) | [GitHub](https://github.com/Vitor-ALucn)

---
*Desenvolvido como parte do aprimoramento contínuo em arquitetura de software, integração de APIs e segurança da informação.*
