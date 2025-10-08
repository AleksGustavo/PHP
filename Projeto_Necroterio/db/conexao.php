<?php
// Configurações do Banco de Dados
// Junte o host e a porta, como no seu projeto que funciona.
$host_com_porta = 'localhost:3307'; 
$usuario = 'root';
// Corrigido: Se não houver senha, use string vazia ('') ou null.
// A string 'null' é uma senha literal, o que causa o erro 1045.
$senha = ''; 
$banco = 'necroterio_db';

// DSN (Data Source Name) - String de conexão para o PDO
// Use o host completo na DSN, sem a necessidade de $port.
$dsn = "mysql:host=$host_com_porta;dbname=$banco;charset=utf8";

// Opções de configuração para o PDO
// Mantive as opções de boas práticas.
$opcoes = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" 
];

try {
    // Tenta Conectar usando PDO
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
    
    // Conexão bem-sucedida!

} catch (PDOException $e) {
    // Se a conexão falhar, exibe a mensagem de erro
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}
?>