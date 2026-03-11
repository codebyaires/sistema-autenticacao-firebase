<?php
/**
 * @file dashboard.php
 * @description Interface da área logada e validação de sessão ativa do usuário.
 * @author Victor Gabriel
 * @version 1.2.0
 */

// Inicia a sessão para checar se o usuário passou pelo verificar.php
session_start();

// Se a variável 'logado' não existir ou for falsa, chuta de volta para o login
if (!isset($_SESSION["logado"]) || $_SESSION["logado"] !== true) {
    header("Location: login.php");
    exit;
}

// Recupera dados da sessão
$nome = $_SESSION['nome'] ?? 'Usuário';
$email = $_SESSION['email'] ?? '';
$uid = $_SESSION['uid'] ?? '';
$foto = $_SESSION['foto'] ?? '';
$inicio = $_SESSION['inicio'] ?? time();

// Calcula saudação baseada no horário
$hora = date('H');
if ($hora >= 5 && $hora < 12) {
    $saudacao = 'Bom dia';
    $icone = '🌅';
} elseif ($hora >= 12 && $hora < 18) {
    $saudacao = 'Boa tarde';
    $icone = '☀️';
} else {
    $saudacao = 'Boa noite';
    $icone = '🌙';
}

// Calcula tempo de sessão
$tempoSessao = time() - $inicio;
$minutos = floor($tempoSessao / 60);
$segundos = $tempoSessao % 60;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Victor Gabriel">
    <title>Painel | Bem-vindo!</title>
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <div class="logo">🔐 Meu Sistema</div>
            <button class="btn-logout" onclick="logout()">🚪 Sair</button>
        </div>

        <div class="main-content">
            <div class="welcome-card">
                <div class="avatar-container">
                    <?php if (!empty($foto)): ?>
                        <img src="<?= htmlspecialchars($foto) ?>" alt="Foto" class="avatar">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?= strtoupper(substr($nome, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="status-indicator"></div>
                </div>

                <h1 class="welcome-title">
                    <?= $icone ?> <?= $saudacao ?>, <?= htmlspecialchars($nome) ?>!
                </h1>
                
                <p class="welcome-subtitle">
                    Que bom ter você de volta! Aqui estão suas informações:
                </p>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">📧</div>
                    <div class="info-content">
                        <h3>E-mail</h3>
                        <p><?= htmlspecialchars($email) ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">🆔</div>
                    <div class="info-content">
                        <h3>ID do Usuário</h3>
                        <p class="mono"><?= htmlspecialchars(substr($uid, 0, 20)) ?>...</p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">🕐</div>
                    <div class="info-content">
                        <h3>Login Realizado</h3>
                        <p><?= date('d/m/Y', $inicio) ?></p>
                        <p class="small"><?= date('H:i:s', $inicio) ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">⏱️</div>
                    <div class="info-content">
                        <h3>Tempo de Sessão</h3>
                        <p id="tempo-sessao"><?= $minutos ?>m <?= $segundos ?>s</p>
                        <p class="small">Ativo agora</p>
                    </div>
                </div>
            </div>

            <div class="actions-section">
                <h2>⚡ Ações Rápidas</h2>
                <div class="actions-grid">
                    <a href="#" class="action-card">
                        <span class="action-icon">👤</span>
                        <span>Meu Perfil</span>
                    </a>
                    <a href="#" class="action-card">
                        <span class="action-icon">⚙️</span>
                        <span>Configurações</span>
                    </a>
                    <a href="#" class="action-card">
                        <span class="action-icon">📊</span>
                        <span>Relatórios</span>
                    </a>
                    <a href="logout.php" class="action-card danger">
                        <span class="action-icon">🚪</span>
                        <span>Sair do Sistema</span>
                    </a>
                </div>
            </div>

            <div class="notification">
                <span class="notification-icon">✅</span>
                <span>Login realizado com sucesso em <?= date('d/m/Y \à\s H:i', $inicio) ?></span>
            </div>
        </div>

        <div class="footer">
            <p>&copy; <?= date('Y') ?> Meu Sistema. Todos os direitos reservados.</p>
            <p class="credits">
                Desenvolvido com 💻 por <a href="https://www.linkedin.com/in/victor-aires-93621636a" target="_blank">Victor Gabriel</a>
            </p>
        </div>
        </div>
    </div>

    <script>
        // Atualiza tempo de sessão em tempo real
        const inicioSessao = <?= $inicio ?>;
        
        function atualizarTempoSessao() {
            const agora = Math.floor(Date.now() / 1000);
            const diff = agora - inicioSessao;
            const minutos = Math.floor(diff / 60);
            const segundos = diff % 60;
            
            document.getElementById('tempo-sessao').innerText = 
                `${minutos}m ${segundos.toString().padStart(2, '0')}s`;
        }
        
        setInterval(atualizarTempoSessao, 1000);
        atualizarTempoSessao();

        // Função de logout
        function logout() {
            if (confirm('Deseja realmente sair do sistema?')) {
                window.location.href = 'logout.php';
            }
        }

        // Animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.info-card, .action-card').forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
                el.classList.add('animate-in');
            });
        });
    </script>
</body>
</html>