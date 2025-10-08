<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM produto WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header("Location: produto_read.php");
exit();