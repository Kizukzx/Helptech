<?php
// Ajuste estes dados para a configuração do seu MySQL.
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "helptech"; // coloque aqui o nome do banco que você já criou
$porta = 3307;
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($servidor, $usuario, $senha, $banco, $porta);
if ($conn->connect_error) {
    error_log("Falha de conexão MySQL: " . $conn->connect_error);
    http_response_code(500);
    exit("Não foi possível conectar ao banco de dados. Confira as configurações em sistema/config/conexao.php.");
}
$conn->set_charset("utf8mb4");
// Chave da aplicação para criptografia do CPF. Em produção, mantenha fora do repositório.
define('APP_ENCRYPTION_KEY', 'troque-esta-chave-por-uma-chave-secreta-de-32-caracteres');
