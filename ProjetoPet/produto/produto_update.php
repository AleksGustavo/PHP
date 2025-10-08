<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: produto_read.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE produto SET nome = ?, descricao = ?, preco = ?, estoque = ?, data_compra = ?, data_validade = ?, lote = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['nome'],
        $_POST['descricao'],
        $_POST['preco'],
        $_POST['estoque'],
        $_POST['data_compra'] ?: null,
        $_POST['data_validade'] ?: null,
        $_POST['lote'],
        $id
    ]);
    header("Location: produto_read.php");
    exit();
}

$sql = "SELECT * FROM produto WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: produto_read.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
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
    <h2>Editar Produto</h2>
    <div class="card p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-12">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                </div>
                <div class="col-12">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="3"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label for="preco" class="form-label">Preço *</label>
                    <input type="number" step="0.01" id="preco" name="preco" class="form-control" value="<?= htmlspecialchars($produto['preco']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="estoque" class="form-label">Quantidade em Estoque *</label>
                    <input type="number" id="estoque" name="estoque" class="form-control" value="<?= htmlspecialchars($produto['estoque']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="data_compra" class="form-label">Data da Compra</label>
                    <input type="date" id="data_compra" name="data_compra" class="form-control" value="<?= htmlspecialchars($produto['data_compra']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="data_validade" class="form-label">Data de Validade</label>
                    <input type="date" id="data_validade" name="data_validade" class="form-control" value="<?= htmlspecialchars($produto['data_validade']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="lote" class="form-label">Lote</label>
                    <input type="text" id="lote" name="lote" class="form-control" value="<?= htmlspecialchars($produto['lote']) ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="produto_read.php" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>