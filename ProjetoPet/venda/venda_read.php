<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

$sql = "SELECT v.id, v.data_venda, v.total, c.nome, c.sobrenome 
        FROM venda v 
        LEFT JOIN cliente c ON v.cliente_id = c.id
        ORDER BY v.data_venda DESC";
$stmt = $pdo->query($sql);
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Vendas</title>
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
                    <a class="nav-link" href="../produto/produto_read.php">Produtos</a>
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
        <h2>Histórico de Vendas</h2>
        <div>
            <a href="venda_create.php" class="btn btn-primary me-2">Nova Venda</a>
            <a href="../index.php" class="btn btn-secondary">Voltar ao Início</a>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($vendas) > 0): ?>
                        <?php foreach ($vendas as $venda): ?>
                            <tr>
                                <td><?= htmlspecialchars($venda['id']) ?></td>
                                <td>
                                    <?php if ($venda['nome']): ?>
                                        <?= htmlspecialchars($venda['nome'] . ' ' . $venda['sobrenome']) ?>
                                    <?php else: ?>
                                        (Não informado)
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($venda['data_venda']))) ?></td>
                                <td>R$ <?= number_format($venda['total'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="venda_detalhe.php?id=<?= $venda['id'] ?>" class="btn btn-sm btn-info text-white me-2">Detalhes</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma venda encontrada.</td>
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