<?php
// ============================================
// Arquivo: cadastro_usuario.php (O DASHBOARD)
// ============================================
session_start();

// 1. A PROTEÇÃO: Se não passou pelo verificar.php, volta pro login!
if (!isset($_SESSION["logado"]) || $_SESSION["logado"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

// Buscar todos os usuários para listar na tabela (Apenas leitura)
$sql = "SELECT id, nome, email, criado_em FROM usuario ORDER BY id DESC";
$usuarios = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Projeto SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 min-h-screen flex">

    <?php require_once "menu.php"; ?>

    <main class="ml-64 flex-1 p-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Bem-vindo, <?php echo $_SESSION['nome'] ?? 'Usuário'; ?>!</h2>
                <p class="text-gray-500 mt-1">Este é o painel restrito do sistema.</p>
            </div>
            <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Sair do Sistema</a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Usuários do MySQL</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="px-4 py-3 text-left rounded-tl-lg">ID</th>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left rounded-tr-lg">Criado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($usuarios): while ($u = mysqli_fetch_assoc($usuarios)): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($u["id"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($u["nome"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($u["email"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-gray-500"><?php echo htmlspecialchars($u["criado_em"], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>