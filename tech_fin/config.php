<?php
// Definindo as credenciais do banco de dados
$servername = "localhost";
$username = "root";    // Usuário root
$password = "";        // Senha em branco para o root, se configurado assim
$dbname = "dba_tech_fin";  // Nome do banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>