<?php
// 1. Destrói a sessão do PHP no Servidor
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Saindo...</title>
</head>
<body style="background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif;">
    <h2 style="color: #333;">Encerrando sessão com segurança...</h2>

    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyBk6g1ooxLFriT4jzxEmrd1IuC0gykg3pA",
            authDomain: "fir-outliner.firebaseapp.com"
        };
        firebase.initializeApp(firebaseConfig);
        
        // Desloga do Firebase e arremessa de volta pro login.php
        firebase.auth().signOut().then(() => {
            window.location.href = "login.php"; // MUDANÇA AQUI
        }).catch(() => {
            window.location.href = "login.php"; // MUDANÇA AQUI
        });
    </script>
</body>
</html>