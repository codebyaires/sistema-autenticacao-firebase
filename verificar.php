<?php
// Avisa que a resposta será no formato JSON (que o JavaScript entende fácil)
header('Content-Type: application/json');

// Recebe o "pacote" enviado pelo script.js
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// Verifica se o JavaScript realmente mandou o token
if (isset($input['token']) && !empty($input['token'])) {
    
    $idToken = $input['token'];

    // --- AQUI É ONDE O PHP VALIDARIA O TOKEN REALMENTE NO FUTURO ---

    // Resposta de Sucesso que será devolvida ao JavaScript
    $resposta = [
        "status" => "sucesso",
        "mensagem" => "Token recebido pelo PHP com sucesso!",
        "dados_servidor" => [
            "usuario" => "Usuário Autenticado",
        ]
    ];
    echo json_encode($resposta);

} else {
    // Resposta de Erro caso tentem acessar o PHP direto pelo navegador
    http_response_code(401);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Acesso negado. Cadê o token?"
    ]);
}
?>