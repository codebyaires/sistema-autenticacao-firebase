/**
 * @file app.js
 * @description Lógica de autenticação frontend via Firebase e comunicação com a API PHP.
 * @author Victor Gabriel, Peterson Ruivo e Vitor Augusto
 * @version 1.1.0
 */

// ============================================
// CONFIGURAÇÃO DO FIREBASE
// ============================================
const firebaseConfig = {
    apiKey: "AIzaSyCtl7o0IjKliLC-fnf-RekT9toi6X1i8Dk",
    authDomain: "aula4-teste1.firebaseapp.com",
    projectId: "aula4-teste1",
    storageBucket: "aula4-teste1.firebasestorage.app",
    messagingSenderId: "295101879405",
    appId: "1:295101879405:web:b99346d5c9907a7fd65b64"
};

firebase.initializeApp(firebaseConfig);
const auth = firebase.auth();
const googleProvider = new firebase.auth.GoogleAuthProvider();

// Assinatura silenciosa para desenvolvedores no console
console.log(
    "%c🚀 Autenticação Inicializada\n%cDev: Victor Gabriel", 
    "color: #FFCA28; font-size: 14px; font-weight: bold;", 
    "color: #4CAF50; font-size: 12px;"
);

// ============================================
// FUNÇÕES UTILITÁRIAS
// ============================================

function setStatus(message, type = 'info') {
    const status = document.getElementById('status');
    status.innerText = message;
    status.className = `show ${type}`;
}

// Diferencial: Tradutor de erros do Firebase para melhorar a UX
function traduzirErroFirebase(codigoErro) {
    const erros = {
        'auth/invalid-email': 'O formato do e-mail é inválido.',
        'auth/user-disabled': 'Esta conta de usuário foi desativada.',
        'auth/user-not-found': 'Usuário não encontrado. Verifique o e-mail.',
        'auth/wrong-password': 'A senha está incorreta.',
        'auth/popup-closed-by-user': 'A janela de login do Google foi fechada.',
        'auth/popup-blocked': 'O popup do Google foi bloqueado pelo navegador.',
        'auth/network-request-failed': 'Falha na conexão. Verifique sua internet.'
    };
    return erros[codigoErro] || 'Ocorreu um erro desconhecido na autenticação.';
}

// ============================================
// LÓGICA DE LOGIN
// ============================================

async function loginEmail() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (!email || !password) {
        setStatus('⚠️ Preencha e-mail e senha', 'error');
        return;
    }

    try {
        setStatus('🔄 Autenticando...', 'info');
        const result = await auth.signInWithEmailAndPassword(email, password);
        const token = await result.user.getIdToken();
        await verificarToken(token);
    } catch (error) {
        console.error('Erro Auth:', error);
        // Aplica a tradução amigável em vez do erro em inglês
        setStatus('❌ ' + traduzirErroFirebase(error.code), 'error');
    }
}

async function loginGoogle() {
    try {
        setStatus('🔄 Abrindo Google...', 'info');
        const result = await auth.signInWithPopup(googleProvider);
        const token = await result.user.getIdToken();
        await verificarToken(token);
    } catch (error) {
        console.error('Erro Google:', error);
        setStatus('❌ ' + traduzirErroFirebase(error.code), 'error');
    }
}

async function verificarToken(token) {
    try {
        const response = await fetch('verificar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ token: token })
        });

        const result = await response.json();

        if (result.success) {
            setStatus('✅ Login realizado! Redirecionando...', 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1500);
        } else {
            setStatus('❌ ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Erro PHP:', error);
        setStatus('❌ Erro de comunicação com servidor', 'error');
    }
}

document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') loginEmail();
});