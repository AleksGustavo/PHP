<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

$clientes = [];
$termo_busca = '';
$busca_realizada = false; // Indica se o usuário tentou buscar algo.

// Adiciona variável para mensagem de sucesso do cadastro
$success = '';
if (isset($_GET['cadastro_sucesso']) && $_GET['cadastro_sucesso'] == 'true') {
    $success = "Cliente cadastrado com sucesso!";
}

if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $busca_realizada = true;
    $termo_busca = trim($_GET['q']);

    // Busca por ID (exato), CPF (exato), ou Nome/Sobrenome (parcial)
    $sql = "SELECT * FROM cliente WHERE id = ? OR cpf = ? OR nome LIKE ? OR sobrenome LIKE ? ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $termo_like = "%$termo_busca%";

    // Executa com ID, CPF, Nome LIKE, Sobrenome LIKE
    $stmt->execute([$termo_busca, $termo_busca, $termo_like, $termo_like]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Clientes</title>
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
                        <a class="nav-link" href="cliente_read.php">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">

        <?php if (isset($success) && !empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Clientes</h2>
            <div>
                <a href="./cliente_create.php" class="btn btn-primary">Novo Cliente</a>
                <a href="../iniciar.php" class="btn btn-secondary">Voltar ao Início</a>
            </div>
        </div>

        <div class="row mb-4 justify-content-center">
            <div class="col-md-6 col-lg-6">
                <form method="GET" class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Buscar por ID, Nome, Sobrenome ou CPF" aria-label="Search" name="q" id="search-input" value="<?= htmlspecialchars($termo_busca) ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </form>
            </div>
        </div>

        <?php if ($busca_realizada || count($clientes) > 0): ?>

            <div class="card p-3">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>CPF</th>
                                <th>Nome Completo</th>
                                <th>Celular</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clientes) > 0): ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cliente['id']) ?></td>
                                        <td><?= htmlspecialchars($cliente['cpf'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($cliente['nome'] . ' ' . $cliente['sobrenome']) ?></td>
                                        <td><?= htmlspecialchars($cliente['celular']) ?></td>
                                        
                                        <td class="text-nowrap">
                                            <div class="d-flex flex-nowrap">
                                                <a href="cliente_update.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-secondary me-2">Editar</a>
                                                
                                                <?php if (is_admin()): ?>
                                                    <a href="cliente_delete.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-danger me-2" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                                                <?php endif; ?>
                                                
                                                <a href="../pet/pet_create.php?cliente_id=<?= $cliente['id'] ?>" class="btn btn-sm btn-info text-white me-2">Adicionar Pet</a>
                                                <a href="../pet/pet_read.php?cliente_id=<?= $cliente['id'] ?>" class="btn btn-sm btn-success text-white">Ver Pets</a>
                                            </div>
                                        </td>
                                        
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum cliente encontrado com o termo "<?= htmlspecialchars($termo_busca) ?>".</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif (empty($success)): ?>
            <div class="alert alert-info text-center mt-4">
                Utilize o campo de busca acima para encontrar clientes por ID, Nome, Sobrenome ou CPF.
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('search-input').addEventListener('input', function() {
            if (this.value === '') {
                window.location.href = 'cliente_read.php';
            }
        });
    </script>
</body>

</html>