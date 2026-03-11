<?php
/**
 * @file conexao.php
 * @description Conexão com o banco de dados MySQL local.
 * @author Victor Gabriel
 */

$servidor = "localhost";
$usuario = "root";
$senha = ""; // No XAMPP, a senha do root por padrão é vazia
$banco = "autenticacao_firebase"; // Nome do seu banco de dados

// Cria a conexão usando MySQLi (o mesmo padrão que usamos nos seus outros arquivos)
$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);

// Verifica se deu algum erro na conexão
if (!$conexao) {
    die("Erro de conexão com o MySQL: " . mysqli_connect_error());
}

// Configura o padrão de caracteres para aceitar acentos (ç, ã, é) sem bugar
mysqli_set_charset($conexao, "utf8mb4");
?>