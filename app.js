/**
 * @file app.js
 * @description Lógica de autenticação frontend via Firebase e comunicação com a API PHP.
 * @author Victor Gabriel, Peterson Ruivo e Vitor Augusto
 * @version 1.2.0
 */

// ============================================
// CONFIGURAÇÃO DO FIREBASE
// ============================================
const firebaseConfig = {
    apiKey: "AIzaSyBk6g1ooxLFriT4jzxEmrd1IuC0gykg3pA",
    authDomain: "fir-outliner.firebaseapp.com",
    projectId: "fir-outliner",
    storageBucket: "fir-outliner.firebasestorage.app",
    messagingSenderId: "67431209635",
    appId: "1:67431209635:web:2375d2db80b11873d9fd16",
    measurementId: "G-TMZVJG7SYB"
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
    if (status) {
        status.innerText = message;
        status.className = `show ${type}`;
    }
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
        'auth/network-request-failed': 'Falha na conexão. Verifique sua internet.',
        'auth/email-already-in-use': 'Este e-mail já está cadastrado no sistema.',
        'auth/weak-password': 'A senha é muito fraca. Use pelo menos 6 caracteres.'
    };
    return erros[codigoErro] || 'Ocorreu um erro desconhecido na autenticação.';
}

// ============================================
// LÓGICA DE LOGIN (login.php)
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
        
        // 1. Tenta fazer o login
        const result = await auth.signInWithEmailAndPassword(email, password);

        // 2. TRAVA DE SEGURANÇA: Verifica se o email foi confirmado
        if (!result.user.emailVerified) {
            await auth.signOut(); // Desloga o usuário
            setStatus('⚠️ Acesso negado. Verifique sua caixa de entrada (ou spam) e clique no link de ativação.', 'error');
            return; // Para o código aqui!
        }

        // 3. Se estiver tudo ok, gera o token e manda pro PHP
        const token = await result.user.getIdToken();
        await verificarToken(token);
        
    } catch (error) {
        console.error('Erro Auth:', error);
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

// ============================================
// LÓGICA DE CADASTRO (criar_conta.html)
// ============================================

async function criarContaEmail() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (!email || !password) {
        setStatus('⚠️ Preencha e-mail e senha', 'error');
        return;
    }

    try {
        setStatus('🔄 Criando conta...', 'info');
        
        // 1. Cria a conta no Firebase
        const result = await auth.createUserWithEmailAndPassword(email, password);
        
        // 2. Envia o e-mail de verificação oficial do Firebase
        await result.user.sendEmailVerification();
        
        // 3. Desloga o usuário imediatamente para ele não acessar o painel
        await auth.signOut();

        setStatus('📧 Conta criada! Verifique seu e-mail para ativar.', 'success');
        
        // Redireciona para a tela de login após 4 segundos
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 4000);

    } catch (error) {
        console.error('Erro Cadastro:', error);
        setStatus('❌ ' + traduzirErroFirebase(error.code), 'error');
    }
}

// ============================================
// VALIDAÇÃO COM O PHP
// ============================================

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

// Facilidade: Usar o "Enter" para enviar o formulário
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const btnLogin = document.querySelector('.btn-email');
        if (btnLogin && btnLogin.innerText.includes('Entrar')) {
            loginEmail();
        } else if (btnLogin && btnLogin.innerText.includes('Cadastrar')) {
            criarContaEmail();
        }
    }
});