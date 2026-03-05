<?php
// Arquivo: logado.php
session_start();

// Verifica se a variável de sessão criada pelo verificar.php existe
if (!isset($_SESSION["logado"]) || $_SESSION["logado"] !== true) {
    // Se não estiver logado, chuta para a tela de login
    header("Location: login.php");
    exit;
}
?>