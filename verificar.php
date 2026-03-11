<?php
/**
 * @file verificar.php
 * @description Validação de token JWT, gerador de sessão e Sincronização automática com MySQL.
 * @author Victor Gabriel
 * @version 1.2.0
 */

ob_start();
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function responder($dados, $codigo = 200) {
    if (ob_get_length()) ob_clean();
    http_response_code($codigo);
    echo json_encode($dados);
    exit;
}

// ============================================
// CONFIGURAÇÃO DO FIREBASE
// ============================================
$projectId = 'fir-outliner'; 
$apiKey = "AIzaSyBk6g1ooxLFriT4jzxEmrd1IuC0gykg3pA"; 

try {
    // 1. Recebe token do frontend
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? '';
    
    if (empty($token)) {
        responder(['success' => false, 'message' => 'Token não enviado'], 400);
    }
    
    // 2. Valida token usando API REST do Firebase
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$apiKey}";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['idToken' => $token]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        responder(['success' => false, 'message' => 'Token inválido ou expirado'], 401);
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['users'][0])) {
        responder(['success' => false, 'message' => 'Usuário não encontrado'], 401);
    }
    
    $user = $data['users'][0];
    
    // Variáveis que vieram do Firebase
    $uidFirebase = $user['localId'];
    $email = $user['email'] ?? '';
    // Se a pessoa criou conta por e-mail/senha, o Google não dá "Nome", então colocamos um padrão
    $nome = $user['displayName'] ?? 'Novo Usuário'; 
    $foto = $user['photoUrl'] ?? '';
    
    // ============================================
    // 3. SINCRONIZAÇÃO COM O MYSQL (O Pulo do Gato)
    // ============================================
    // Chama a sua conexão com o banco de dados
    require_once "conexao.php";
    
    if (isset($conexao)) {
        // Verifica se o e-mail já existe na tabela 'usuario'
        $stmt = mysqli_prepare($conexao, "SELECT id FROM usuario WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        // Se retornar 0, significa que é o primeiro login dessa pessoa no sistema!
        if (mysqli_stmt_num_rows($stmt) == 0) {
            mysqli_stmt_close($stmt); // Fecha a busca
            
            // Faz o INSERT do novo usuário no seu MySQL local
            // (Assumindo que sua tabela preenche o 'id' e o 'criado_em' automaticamente no MySQL)
            $stmtInsert = mysqli_prepare($conexao, "INSERT INTO usuario (nome, email) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmtInsert, "ss", $nome, $email);
            mysqli_stmt_execute($stmtInsert);
            mysqli_stmt_close($stmtInsert);
        } else {
            // O usuário já existe no MySQL, apenas fecha a consulta
            mysqli_stmt_close($stmt);
        }
    }

    // ============================================
    // 4. CRIA SESSÃO PHP
    // ============================================
    $_SESSION['uid'] = $uidFirebase;
    $_SESSION['email'] = $email;
    $_SESSION['nome'] = $nome;
    $_SESSION['foto'] = $foto;
    $_SESSION['logado'] = true;
    $_SESSION['inicio'] = time();
    
    responder([
        'success' => true,
        'message' => 'Autenticação e sincronização OK'
    ]);
    
} catch (\Exception $e) {
    responder(['success' => false, 'message' => 'Erro interno no servidor de autenticação.'], 500);
}
?>