<?php
session_start();
if (isset($_SESSION["logado"]) && $_SESSION["logado"] === true) {
    header("Location: cadastro_usuario.php");
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

        <form id="formCadastro">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" required placeholder="seu@email.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label for="senha" class="block text-gray-700 font-medium mb-2">Senha</label>
                <input type="password" id="senha" required placeholder="Mínimo 6 caracteres" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
        import { getAuth, createUserWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-auth.js";

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

        function enviarTokenParaPHP(token) {
            console.log("Conta criada! Enviando token para verificar.php...");
            fetch('verificar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: token })
            })
            .then(async response => {
                if (!response.ok) {
                    const txt = await response.text();
                    throw new Error(`Erro HTTP ${response.status}: ${txt}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Resposta PHP:", data);
                if (data.success) {
                    window.location.href = "cadastro_usuario.php"; 
                } else {
                    mostrarErro("Erro no PHP: " + data.message);
                }
            })
            .catch(error => {
                console.error("ERRO FETCH:", error);
                mostrarErro("Falha de comunicação. Abra o Console (F12) para ver os detalhes.");
            });
        }

        function mostrarErro(mensagem) {
            divErro.classList.remove('hidden');
            divErro.innerText = mensagem;
            btnCadastrar.innerHTML = "Cadastrar";
            btnCadastrar.disabled = false;
        }

        formCadastro.addEventListener('submit', (e) => {
            e.preventDefault();
            divErro.classList.add('hidden');
            btnCadastrar.innerHTML = "Criando conta...";
            btnCadastrar.disabled = true;

            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;

            createUserWithEmailAndPassword(auth, email, senha)
                .then((userCredential) => userCredential.user.getIdToken())
                .then((token) => enviarTokenParaPHP(token))
                .catch((error) => {
                    console.error("Erro Firebase:", error);
                    if(error.code === 'auth/email-already-in-use') {
                        mostrarErro("Este e-mail já está em uso.");
                    } else if(error.code === 'auth/weak-password') {
                        mostrarErro("A senha deve ter pelo menos 6 caracteres.");
                    } else {
                        mostrarErro("Erro: " + error.message);
                    }
                });
        });
    </script>
</body>
</html>