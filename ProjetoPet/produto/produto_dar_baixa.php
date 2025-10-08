<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

$sql_produtos = "SELECT id, nome FROM produto ORDER BY nome";
$stmt_produtos = $pdo->query($sql_produtos);
$produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = $_POST['produto_id'];
    $quantidade_baixa = intval($_POST['quantidade']);

    if ($quantidade_baixa > 0) {
        // Pega o estoque atual
        $sql_estoque_atual = "SELECT estoque FROM produto WHERE id = ?";
        $stmt_estoque_atual = $pdo->prepare($sql_estoque_atual);
        $stmt_estoque_atual->execute([$produto_id]);
        $estoque_atual = $stmt_estoque_atual->fetchColumn();

        // Verifica se há estoque suficiente
        if ($estoque_atual >= $quantidade_baixa) {
            $novo_estoque = $estoque_atual - $quantidade_baixa;
            $sql_update = "UPDATE produto SET estoque = ? WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$novo_estoque, $produto_id]);
            header("Location: produto_read.php");
            exit();
        } else {
            $mensagem_erro = "Estoque insuficiente para dar baixa. Estoque atual: {$estoque_atual}";
        }
    } else {
        $mensagem_erro = "A quantidade para dar baixa deve ser maior que zero.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dar Baixa em Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">Petshop - Gestão</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../cliente/cliente_read.php">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="produto_read.php">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Dar Baixa em Produto</h2>
    <div class="card p-4">
        <form method="POST">
            <?php if ($mensagem_erro): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($mensagem_erro) ?>
                </div>
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-12">
                    <label for="produto_id" class="form-label">Produto *</label>
                    <select id="produto_id" name="produto_id" class="form-select" required>
                        <option value="">Selecione um produto...</option>
                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?= htmlspecialchars($produto['id']) ?>">
                                <?= htmlspecialchars($produto['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="quantidade" class="form-label">Quantidade para dar baixa *</label>
                    <input type="number" id="quantidade" name="quantidade" class="form-control" required min="1">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Confirmar Baixa</button>
                    <a href="produto_read.php" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>