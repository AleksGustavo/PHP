<?php
require_once '../includes/conexao.php';

header('Content-Type: application/json');

$termo_busca = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($termo_busca)) {
    echo json_encode(null);
    exit;
}

// Verifica se o termo de busca é numérico (para buscar por ID)
if (is_numeric($termo_busca)) {
    $sql = "SELECT id, nome, preco, estoque FROM produto WHERE id = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$termo_busca]);
} else {
    // Se não, busca por nome
    $termo_like = "%$termo_busca%";
    $sql = "SELECT id, nome, preco, estoque FROM produto WHERE nome LIKE ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$termo_like]);
}

$produto = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($produto);