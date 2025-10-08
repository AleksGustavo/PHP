<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Inicializa a variável para a lista de pets
$pets = [];
$cliente_info = null;

// Verifica se um cliente_id foi passado na URL
if (isset($_GET['cliente_id']) && is_numeric($_GET['cliente_id'])) {
    $cliente_id = intval($_GET['cliente_id']);
    
    // 1. Busca dados do cliente para o cabeçalho
    $stmt_cliente = $pdo->prepare("SELECT nome, sobrenome FROM cliente WHERE id = ?");
    $stmt_cliente->execute([$cliente_id]);
    $cliente_info = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

    // 2. Busca os pets do cliente
    $sql = "SELECT p.*, c.nome as nome_cliente, c.sobrenome as sobrenome_cliente FROM pet p JOIN cliente c ON p.cliente_id = c.id WHERE p.cliente_id = ? ORDER BY p.nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cliente_id]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verifica se o cliente foi encontrado antes de acessar suas propriedades
    if ($cliente_info) {
        $titulo_pagina = "Pets de " . htmlspecialchars($cliente_info['nome'] . ' ' . $cliente_info['sobrenome']);
    } else {
        $titulo_pagina = "Cliente não encontrado";
        // Redirecionar se o cliente_id for inválido mas numerico
        header("Location: pet_read.php");
        exit();
    }

} else {
    // Se nenhum cliente_id for passado, exibe todos os pets
    $sql = "SELECT p.*, c.nome as nome_cliente, c.sobrenome as sobrenome_cliente FROM pet p JOIN cliente c ON p.cliente_id = c.id ORDER BY p.nome ASC";
    $stmt = $pdo->query($sql);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $titulo_pagina = "Todos os Pets Cadastrados";
}

// Mensagem de sucesso (AGORA TRATA OS DOIS TIPOS)
$success_message = '';
if (isset($_GET['cadastro_sucesso'])) {
    $success_message = "Pet cadastrado/atualizado com sucesso!";
} elseif (isset($_GET['exclusao_sucesso'])) {
    $success_message = "Pet excluído com sucesso!";
} elseif (isset($_GET['erro']) && $_GET['erro'] == 'permissao_negada') {
    $success_message = "Ação negada: Apenas Administradores podem excluir pets.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Pets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="../iniciar.php">Pet&Pet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../cliente/cliente_read.php">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pet_read.php">Pets</a>
                </li>
                <?php if (function_exists('is_admin') && is_admin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../usuario/usuario_read.php">Usuários</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= $titulo_pagina ?></h2>
        <div>
            <?php if (isset($cliente_id)): ?>
                <a href="pet_create.php?cliente_id=<?= $cliente_id ?>" class="btn btn-primary">Novo Pet</a>
                <a href="../cliente/cliente_read.php" class="btn btn-secondary">Voltar aos Clientes</a>
            <?php else: ?>
                <a href="../cliente/cliente_read.php" class="btn btn-secondary">Cadastrar Pet (Escolher Cliente)</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-3">
        <?php if (count($pets) > 0): ?>
            <?php foreach ($pets as $pet): 
                // Define o caminho da imagem ou usa um placeholder
                $imagem_src = empty($pet['imagem']) ? '../img/placeholder-pet.png' : '../uploads/pets/' . htmlspecialchars($pet['imagem']);
                // Adicionei um placeholder na pasta 'img' para caso não tenha imagem. Você deve criar este arquivo.
            ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?= $imagem_src ?>" class="card-img-top" alt="Foto de <?= htmlspecialchars($pet['nome']) ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h4 class="card-title text-truncate"><?= htmlspecialchars($pet['nome']) ?></h4>
                        <p class="card-text mb-1 text-muted">
                            <?= htmlspecialchars($pet['especie']) ?><br>
                            <small>Dono: <?= htmlspecialchars($pet['nome_cliente'] . ' ' . $pet['sobrenome_cliente']) ?></small>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-white border-0">
                        <a href="pet_update.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-secondary flex-fill me-2">Editar</a>
                        <?php if (function_exists('is_admin') && is_admin()): ?>
                            <a href="pet_delete.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-danger flex-fill" onclick="return confirm('Tem certeza que deseja excluir o pet <?= htmlspecialchars($pet['nome']) ?>?');">Excluir</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">Nenhum pet encontrado.</div>
            </div>
        <?php endif; ?>
    </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>