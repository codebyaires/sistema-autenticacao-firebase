<?php
session_start();
// Se já estiver logado, manda direto para o Dashboard!
if (isset($_SESSION["logado"]) && $_SESSION["logado"] === true) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Projeto SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Entrar no Sistema</h1>

        <div id="mensagemErro" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm font-medium"></div>

        <form id="formLogin">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" required placeholder="Digite seu email" autocomplete="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label for="senha" class="block text-gray-700 font-medium mb-2">Senha</label>
                <input type="password" id="senha" required placeholder="Digite sua senha" autocomplete="current-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" id="btnEntrar" class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200 mb-4">
                Entrar
            </button>
        </form>

        <div class="flex items-center my-4">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="px-3 text-gray-500 text-sm">ou</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <button type="button" id="btnGoogle" class="w-full bg-white border border-gray-300 text-gray-700 py-2 rounded-lg font-medium hover:bg-gray-50 transition duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Entrar com o Google
        </button>

        <p class="text-center text-gray-600 mt-6 text-sm">
            Ainda não tem uma conta? 
            <a href="criar_conta.php" class="text-blue-600 hover:text-blue-800 font-bold transition duration-150">
                Cadastre-se aqui
            </a>
        </p>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBk6g1ooxLFriT4jzxEmrd1IuC0gykg3pA",
            authDomain: "fir-outliner.firebaseapp.com",
            projectId: "fir-outliner",
            storageBucket: "fir-outliner.firebasestorage.app",
            messagingSenderId: "67431209635",
            appId: "1:67431209635:web:2375d2db80b11873d9fd16"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        const formLogin = document.getElementById('formLogin');
        const btnGoogle = document.getElementById('btnGoogle');
        const btnEntrar = document.getElementById('btnEntrar');
        const divErro = document.getElementById('mensagemErro');

        function enviarTokenParaPHP(token) {
            console.log("Token gerado! Enviando para verificar.php...");
            fetch('verificar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: token })
            })
            .then(async response => {
                if (!response.ok) {
                    const txt = await response.text();
                    throw new Error(`Erro ${response.status}: ${txt}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Resposta do PHP:", data);
                if (data.success) {
                    // MUDANÇA: Redirecionando para o painel correto
                    window.location.href = "dashboard.php";
                } else {
                    mostrarErro("Recusado pelo PHP: " + data.message);
                }
            })
            .catch(error => {
                console.error("ERRO FETCH:", error);
                mostrarErro("Falha de comunicação. Abra o Console (F12) para ver o erro.");
            });
        }

        function mostrarErro(mensagem) {
            divErro.classList.remove('hidden');
            divErro.innerText = mensagem;
            btnEntrar.innerHTML = "Entrar";
            btnEntrar.disabled = false;
        }

        formLogin.addEventListener('submit', (e) => {
            e.preventDefault();
            divErro.classList.add('hidden');
            btnEntrar.innerHTML = "Autenticando...";
            btnEntrar.disabled = true;

            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;

            signInWithEmailAndPassword(auth, email, senha)
                .then((cred) => {
                    // MUDANÇA: Trava de segurança do E-mail Verificado
                    if (!cred.user.emailVerified) {
                        auth.signOut();
                        throw new Error("unverified-email");
                    }
                    return cred.user.getIdToken();
                })
                .then((token) => enviarTokenParaPHP(token))
                .catch((error) => {
                    console.error("Erro Firebase:", error);
                    if (error.message === "unverified-email") {
                        mostrarErro("Acesso negado. Vá no seu e-mail e clique no link de ativação.");
                    } else {
                        mostrarErro("Email ou senha incorretos.");
                    }
                });
        });

        btnGoogle.addEventListener('click', () => {
            divErro.classList.add('hidden');
            signInWithPopup(auth, provider)
                .then((res) => res.user.getIdToken())
                .then((token) => enviarTokenParaPHP(token))
                .catch((error) => {
                    console.error("Erro Google:", error);
                    mostrarErro("Erro ao logar com Google.");
                });
        });
    </script>
</body>
</html>