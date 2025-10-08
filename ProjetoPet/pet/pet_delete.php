<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// NOVO: Restrição: Apenas administradores podem deletar pets
if (!is_admin()) {
    header("Location: pet_read.php?erro=permissao_negada");
    exit();
}

// Verifica se o ID do pet foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: pet_read.php");
    exit();
}

$id = $_GET['id'];
$redirect_id = null; // Variável para armazenar o cliente_id

// Lógica de exclusão do pet: busca dados para apagar a imagem e redirecionar
$stmt_get = $pdo->prepare("SELECT imagem, cliente_id FROM pet WHERE id = ?");
$stmt_get->execute([$id]);
$pet = $stmt_get->fetch(PDO::FETCH_ASSOC);

if ($pet) {
    // 1. Guarda o ID do cliente para redirecionamento
    $redirect_id = $pet['cliente_id'];
    
    // 2. Exclui o arquivo de imagem, se existir
    if (!empty($pet['imagem'])) {
        $filepath = '../uploads/pets/' . $pet['imagem'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
    
    // 3. Exclui o registro do pet
    $stmt = $pdo->prepare("DELETE FROM pet WHERE id = ?");
    $stmt->execute([$id]);
}

// Redireciona para a lista de pets (do cliente, se aplicável) com a mensagem correta de exclusão
if ($redirect_id) {
    header("Location: pet_read.php?cliente_id=" . $redirect_id . "&exclusao_sucesso=true");
} else {
    // Se não tinha cliente_id, redireciona para a lista geral
    header("Location: pet_read.php?exclusao_sucesso=true");
}
exit();
?>