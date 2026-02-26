<?php
// Avisa que a resposta será JSON
header('Content-Type: application/json');

// Recebe o pacote do JavaScript
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// Verifica se tem token
if (isset($input['token']) && !empty($input['token'])) {
    
    $idToken = $input['token'];
    
    // SUA CHAVE API DO FIREBASE PARA O PHP CONSEGUIR PERGUNTAR AO GOOGLE
    $apiKey = "AIzaSyBk6g1ooxLFriT4jzxEmrd1IuC0gykg3pA"; 

    // URL do Google para validar tokens sem precisar do Composer
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=" . $apiKey;

    // Prepara os dados
    $dados = json_encode(["idToken" => $idToken]);

    // Configura o envio
    $opcoes = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => $dados,
            'ignore_errors' => true 
        ]
    ];
    
    // Dispara a pergunta ao Google e pega a resposta
    $contexto  = stream_context_create($opcoes);
    $respostaGoogle = file_get_contents($url, false, $contexto);
    $dadosGoogle = json_decode($respostaGoogle, true);

    // Avalia se o Google confirmou a identidade do usuário
    if (isset($dadosGoogle['users'])) {
        $emailUsuario = $dadosGoogle['users'][0]['email'];
        
        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Token validado com segurança no Back-end!",
            "dados_servidor" => [
                "usuario" => $emailUsuario
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Token inválido ou expirado."
        ]);
    }

} else {
    http_response_code(400);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Nenhum token fornecido."
    ]);
}
?>