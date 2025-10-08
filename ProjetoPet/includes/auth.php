<?php
session_start();

// Verifica se a sessão do usuário não está ativa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); 
    exit();
}

$user_permission = $_SESSION['permissao'] ?? 'funcionario';

function is_admin() {
    return isset($_SESSION['permissao']) && $_SESSION['permissao'] === 'admin';
}
?>