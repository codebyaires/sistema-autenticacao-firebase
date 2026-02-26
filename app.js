// Importa as ferramentas do Firebase direto do Google
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

// SUAS CHAVES REAIS DO FIREBASE
const firebaseConfig = {
  apiKey: "AIzaSyBk6g1ooxLFriT4jzxEmrd1IuC0gykg3pA",
  authDomain: "fir-outliner.firebaseapp.com",
  projectId: "fir-outliner",
  storageBucket: "fir-outliner.firebasestorage.app",
  messagingSenderId: "67431209635",
  appId: "1:67431209635:web:2375d2db80b11873d9fd16",
  measurementId: "G-TMZVJG7SYB"
};

// Inicializa o Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

// Ação do botão de Entrar
document.getElementById('btnLogin').addEventListener('click', () => {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const msgBox = document.getElementById('mensagem');

    msgBox.style.color = "black";
    msgBox.innerText = "Autenticando no Firebase...";

    // Firebase tenta fazer o login
    signInWithEmailAndPassword(auth, email, password)
        .then((userCredential) => {
            msgBox.innerText = "Login aprovado! Gerando token...";
            // Sucesso! Pega o Token de segurança
            return userCredential.user.getIdToken();
        })
        .then((idToken) => {
            // Manda o Token para o seu PHP local
            enviarParaPHP(idToken);
        })
        .catch((error) => {
            msgBox.style.color = "red";
            msgBox.innerText = "Erro ao logar: " + error.message;
        });
});

// Função que envia o token para o XAMPP
function enviarParaPHP(token) {
    const msgBox = document.getElementById('mensagem');
    
    fetch('valida.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token: token })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'sucesso') {
            msgBox.style.color = "green";
            msgBox.innerText = data.mensagem + " \nLogado como: " + data.dados_servidor.usuario;
        } else {
            msgBox.style.color = "red";
            msgBox.innerText = "Erro no PHP: " + data.mensagem;
        }
    })
    .catch(error => {
        msgBox.style.color = "red";
        msgBox.innerText = "Erro ao falar com o PHP. O XAMPP está ligado?";
        console.error(error);
    });
}