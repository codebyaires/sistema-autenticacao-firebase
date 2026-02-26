// 1. Importa as ferramentas do Firebase
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

// 2. Configuração do seu projeto (COLE SUAS CHAVES AQUI)
const firebaseConfig = {
    apiKey: "SUA_API_KEY",
    authDomain: "SEU_PROJETO.firebaseapp.com",
    projectId: "SEU_PROJETO",
    storageBucket: "SEU_PROJETO.appspot.com",
    messagingSenderId: "SEU_SENDER_ID",
    appId: "SEU_APP_ID"
};

// Inicializa o Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

// 3. O que acontece ao clicar no botão "Entrar"
document.getElementById('btnLogin').addEventListener('click', () => {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const msgBox = document.getElementById('mensagem');

    msgBox.style.color = "black";
    msgBox.innerText = "Autenticando...";

    // Firebase tenta fazer o login
    signInWithEmailAndPassword(auth, email, password)
        .then((userCredential) => {
            // Sucesso! Pega o Token de segurança do usuário
            return userCredential.user.getIdToken();
        })
        .then((idToken) => {
            // Manda o Token para o PHP
            enviarParaPHP(idToken);
        })
        .catch((error) => {
            // Se a senha estiver errada ou usuário não existir
            msgBox.style.color = "red";
            msgBox.innerText = "Erro: " + error.message;
        });
});

// 4. Função que despacha o Token para o servidor local (XAMPP)
function enviarParaPHP(token) {
    const msgBox = document.getElementById('mensagem');
    
    fetch('valida.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token: token })
    })
    .then(response => response.json())
    .then(data => {
        // Recebeu a resposta do PHP!
        if (data.status === 'sucesso') {
            msgBox.style.color = "green";
            msgBox.innerText = data.mensagem + " Bem-vindo, " + data.dados_servidor.usuario;
        } else {
            msgBox.style.color = "red";
            msgBox.innerText = "Erro no servidor: " + data.mensagem;
        }
    })
    .catch(error => console.error("Erro de comunicação:", error));
}