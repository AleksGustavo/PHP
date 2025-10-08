<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: venda_read.php");
    exit();
}

$venda_id = $_GET['id'];

// Pega os detalhes da venda principal
$sql_venda = "SELECT v.id, v.data_venda, v.total, c.nome, c.sobrenome 
              FROM venda v 
              LEFT JOIN cliente c ON v.cliente_id = c.id
              WHERE v.id = ?";
$stmt_venda = $pdo->prepare($sql_venda);
$stmt_venda->execute([$venda_id]);
$venda = $stmt_venda->fetch(PDO::FETCH_ASSOC);

if (!$venda) {
    header("Location: venda_read.php");
    exit();
}

// Pega os itens da venda
$sql_itens = "SELECT vi.*, p.nome as nome_produto
              FROM venda_item vi
              JOIN produto p ON vi.produto_id = p.id
              WHERE vi.venda_id = ?";
$stmt_itens = $pdo->prepare($sql_itens);
$stmt_itens->execute([$venda_id]);
$itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Venda</title>
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
    <h2>Detalhes da Venda #<?= htmlspecialchars($venda['id']) ?></h2>
    <div class="card p-4 mb-4">
        <p><strong>Cliente:</strong> 
            <?php if ($venda['nome']): ?>
                <?= htmlspecialchars($venda['nome'] . ' ' . $venda['sobrenome']) ?>
            <?php else: ?>
                (Não informado)
            <?php endif; ?>
        </p>
        <p><strong>Data da Venda:</strong> <?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($venda['data_venda']))) ?></p>
        <p><strong>Total da Venda:</strong> R$ <?= number_format($venda['total'], 2, ',', '.') ?></p>
    </div>

    <h4>Itens da Venda</h4>
    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($itens) > 0): ?>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nome_produto']) ?></td>
                                <td><?= htmlspecialchars($item['quantidade']) ?></td>
                                <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum item encontrado para esta venda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="venda_read.php" class="btn btn-secondary">Voltar</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>