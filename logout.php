<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Saindo...</title>
</head>
<body style="background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif;">
    
    <h2>Encerrando sessão...</h2>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
        import { getAuth, signOut } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-auth.js";

        // Cole suas credenciais aqui
        const firebaseConfig = {
            apiKey: "AIzaSyBk6g1ooxLFriT4jzxEmrd1IuC0gykg3pA",
            authDomain: "fir-outliner.firebaseapp.com",
            projectId: "fir-outliner"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        // Desloga do Firebase e redireciona
        signOut(auth).then(() => {
            window.location.href = "login.php";
        }).catch((error) => {
            alert("Erro ao sair: " + error.message);
        });
    </script>
</body>
</html>