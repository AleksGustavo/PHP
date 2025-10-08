<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

$sql = "SELECT * FROM produto ORDER BY nome";
$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos</title>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Lista de Produtos</h2>
        <div>
            <a href="produto_create.php" class="btn btn-primary me-2">Novo Produto</a>
            <a href="produto_dar_baixa.php" class="btn btn-warning me-2">Dar Baixa em Produto</a>
            <a href="../index.php" class="btn btn-secondary">Voltar ao Início</a>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Data Compra</th>
                        <th>Data Validade</th>
                        <th>Lote</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produtos) > 0): ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?= htmlspecialchars($produto['id']) ?></td>
                                <td><?= htmlspecialchars($produto['nome']) ?></td>
                                <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($produto['estoque']) ?></td>
                                <td><?= htmlspecialchars($produto['data_compra']) ?></td>
                                <td><?= htmlspecialchars($produto['data_validade']) ?></td>
                                <td><?= htmlspecialchars($produto['lote']) ?></td>
                                <td>
                                    <a href="produto_update.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-secondary me-2">Editar</a>
                                    <a href="produto_delete.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir o produto?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Nenhum produto encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>