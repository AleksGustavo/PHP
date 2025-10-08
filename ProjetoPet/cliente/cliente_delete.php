<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// NOVO: Restrição: Apenas administradores podem deletar clientes
if (!is_admin()) {
    // Redireciona de volta para a leitura, possivelmente com uma mensagem de erro
    header("Location: cliente_read.php?erro=permissao_negada");
    exit();
}

// Verifica se o ID do cliente foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: cliente_read.php");
    exit();
}

$id = $_GET['id'];

// Prepara e executa a query de exclusão
$stmt = $pdo->prepare("DELETE FROM cliente WHERE id = ?");
$stmt->execute([$id]);

header("Location: cliente_read.php");
exit();
?>