<?php
/**
 * @file verificar.php
 * @description Validação de token JWT do Firebase via API REST e gerador de sessão PHP.
 * @author Victor Gabriel, Peterson Ruivo & Vitor Augusto]
 * @version 1.1.0
 */

// verificar.php
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
$projectId = 'aula4-teste1'; // Coloque seu Project ID do Firebase
$apiKey = "AIzaSyCtl7o0IjKliLC-fnf-RekT9toi6X1i8Dk";       // Coloque sua API Key do Firebase

try {
    // Recebe token do frontend
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? '';
    
    if (empty($token)) {
        responder(['success' => false, 'message' => 'Token não enviado'], 400);
    }
    
    // ============================================
    // VALIDA TOKEN USANDO API REST DO FIREBASE
    // ============================================
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
    
    // ============================================
    // CRIA SESSÃO PHP
    // ============================================
    $_SESSION['uid'] = $user['localId'];
    $_SESSION['email'] = $user['email'] ?? '';
    $_SESSION['nome'] = $user['displayName'] ?? 'Usuário';
    $_SESSION['foto'] = $user['photoUrl'] ?? '';  // Foto do Google
    $_SESSION['logado'] = true;
    $_SESSION['inicio'] = time();
    
    // Resposta de Sucesso com sua assinatura embutida (o Easter Egg técnico)
    responder([
        'success' => true,
        'message' => 'Autenticação OK',
        '_meta' => [
            'author' => 'Victor Gabriel, Peterson Ruivo & Vitor Augusto]',
            'version' => '1.1.0'
        ],
        'dados' => [
            'email' => $_SESSION['email'],
            'nome' => $_SESSION['nome']
        ]
    ]);
    
} catch (\Exception $e) {
    responder(['success' => false, 'message' => 'Erro interno no servidor de autenticação.'], 500);
}
?>