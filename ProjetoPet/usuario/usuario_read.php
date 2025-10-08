<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Restrição: Apenas administradores podem acessar esta página
if (!is_admin()) {
    header("Location: ../iniciar.php");
    exit();
}

// Lógica para mensagens de sucesso
$success_message = '';
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'cadastro') {
    $success_message = 'Usuário cadastrado com sucesso!';
} elseif (isset($_GET['sucesso']) && $_GET['sucesso'] == 'atualizacao') {
    $success_message = 'Usuário atualizado com sucesso!';
} elseif (isset($_GET['sucesso']) && $_GET['sucesso'] == 'exclusao') {
    $success_message = 'Usuário excluído com sucesso!';
}

// Busca todos os usuários, ordenando Admin primeiro
$stmt = $pdo->query("SELECT id, nome, username, permissao FROM usuario ORDER BY permissao DESC, nome ASC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca dados do Admin logado para a seção de Mudança de Senha
$stmt_admin = $pdo->prepare("SELECT id, nome FROM usuario WHERE id = ?");
$stmt_admin->execute([$_SESSION['user_id']]);
$admin_data = $stmt_admin->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">...</nav>
<div class="container mt-5">
    <h2>Gerenciamento de Usuários</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success mt-3"><?= $success_message ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Lista de Contas</h4>
        <div>
            <a href="usuario_create.php" class="btn btn-primary me-2">Novo Funcionário</a>
            <a href="admin_mudar_senha.php" class="btn btn-warning">Mudar Minha Senha</a> 
        </div>
    </div>
    
    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Permissão</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['nome']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['permissao'] == 'admin' ? 'danger' : 'success' ?>">
                                        <?= $user['permissao'] == 'admin' ? 'ADMINISTRADOR' : 'Funcionário' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="usuario_update.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-secondary me-2">Editar</a>
                                        <a href="usuario_delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir o usuário <?= htmlspecialchars($user['username']) ?>?');">Excluir</a>
                                    <?php else: ?>
                                        <span class="text-muted">Conta Logada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
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