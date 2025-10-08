<?php
require_once 'db/conexao.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); 

    $sql = "DELETE FROM corpos WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$id])) {
        header("Location: index.php?status=exclusao_sucesso");
        exit();
    } else {
        header("Location: index.php?status=exclusao_erro");
        exit();
    }
} else {
    header("Location: index.php?status=id_invalido");
    exit();
}

$pdo = null;
?>