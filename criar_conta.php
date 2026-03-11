<?php
session_start();
// Se ele já estiver logado, manda pro painel
if (isset($_SESSION["logado"]) && $_SESSION["logado"] === true) {
    header("Location: dashboard.php"); // MUDANÇA: Arrumado para dashboard.php
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — Projeto SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Criar Nova Conta</h2>
        
        <div id="mensagemErro" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm font-medium"></div>
        <div id="mensagemSucesso" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm font-medium"></div>

        <form id="formCadastro">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" required placeholder="seu@email.com" autocomplete="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label for="senha" class="block text-gray-700 font-medium mb-2">Senha</label>
                <input type="password" id="senha" required placeholder="Mínimo 6 caracteres" autocomplete="new-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" id="btnCadastrar" class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200 mb-4">
                Cadastrar
            </button>
        </form>

        <p class="text-center text-gray-600 mt-4 text-sm">
            Já tem uma conta? 
            <a href="login.php" class="text-blue-600 hover:text-blue-800 font-bold transition duration-150">
                Faça login aqui
            </a>
        </p>
    </div>

    <script type="module">
        // Importando a função sendEmailVerification
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
        import { getAuth, createUserWithEmailAndPassword, sendEmailVerification } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-auth.js";

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

        const formCadastro = document.getElementById('formCadastro');
        const btnCadastrar = document.getElementById('btnCadastrar');
        const divErro = document.getElementById('mensagemErro');
        const divSucesso = document.getElementById('mensagemSucesso');

        function mostrarErro(mensagem) {
            divSucesso.classList.add('hidden');
            divErro.classList.remove('hidden');
            divErro.innerText = mensagem;
            btnCadastrar.innerHTML = "Cadastrar";
            btnCadastrar.disabled = false;
        }

        function mostrarSucesso(mensagem) {
            divErro.classList.add('hidden');
            divSucesso.classList.remove('hidden');
            divSucesso.innerText = mensagem;
        }

        formCadastro.addEventListener('submit', (e) => {
            e.preventDefault();
            divErro.classList.add('hidden');
            divSucesso.classList.add('hidden');
            btnCadastrar.innerHTML = "Criando conta e enviando e-mail...";
            btnCadastrar.disabled = true;

            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;

            // 1. Cria a conta no Firebase
            createUserWithEmailAndPassword(auth, email, senha)
                .then((userCredential) => {
                    // 2. Dispara o e-mail de verificação oficial
                    return sendEmailVerification(userCredential.user);
                })
                .then(() => {
                    // 3. Desloga a pessoa imediatamente (para travar o acesso não verificado)
                    return auth.signOut();
                })
                .then(() => {
                    // 4. Mostra o aviso e joga de volta pro login
                    mostrarSucesso("Conta criada! Verifique sua caixa de entrada (ou spam) para ativar.");
                    setTimeout(() => {
                        window.location.href = "login.php"; 
                    }, 4000);
                })
                .catch((error) => {
                    console.error("Erro Firebase:", error);
                    if(error.code === 'auth/email-already-in-use') {
                        mostrarErro("Este e-mail já está em uso.");
                    } else if(error.code === 'auth/weak-password') {
                        mostrarErro("A senha deve ter pelo menos 6 caracteres.");
                    } else {
                        mostrarErro("Erro ao cadastrar: " + error.message);
                    }
                });
        });
    </script>
</body>
</html>