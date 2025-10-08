<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Restrição: Apenas administradores podem acessar esta página
if (!is_admin()) {
    header("Location: ../iniciar.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: usuario_read.php");
    exit();
}

$id = $_GET['id'];
$error = '';

// 1. Busca os dados do usuário
$stmt = $pdo->prepare("SELECT id, nome, username, permissao FROM usuario WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: usuario_read.php");
    exit();
}

// Impede que o admin logado tente se editar nesta tela
if ($user['id'] == $_SESSION['user_id']) {
    header("Location: admin_mudar_senha.php");
    exit();
}

// 2. Processa o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $permissao = $_POST['permissao'];

    if (empty($nome) || empty($username)) {
        $error = "Nome e Usuário são campos obrigatórios.";
    } elseif (!in_array($permissao, ['admin', 'funcionario'])) {
        $error = "Permissão inválida.";
    } else {
        // Verifica se o username já existe para outro ID
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE username = ? AND id != ?");
        $stmt_check->execute([$username, $id]);
        if ($stmt_check->fetchColumn() > 0) {
            $error = "Nome de usuário já existe para outro usuário. Escolha outro.";
        } else {
            // Constrói a query de atualização
            $sql = "UPDATE usuario SET nome = ?, username = ?, permissao = ?";
            $params = [$nome, $username, $permissao];
            
            // Se uma nova senha foi fornecida, adicione-a à query
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $sql .= ", senha = ?";
                $params[] = $hashed_password;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            header("Location: usuario_read.php?sucesso=atualizacao");
            exit();
        }
    }
}
// Dados para preencher o formulário (se houve erro, usa o POST, senão usa o BD)
$nome_form = htmlspecialchars($_POST['nome'] ?? $user['nome']);
$username_form = htmlspecialchars($_POST['username'] ?? $user['username']);
$permissao_form = htmlspecialchars($_POST['permissao'] ?? $user['permissao']);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">...</nav>
<div class="container mt-5">
    <h2>Editar Usuário: <?= htmlspecialchars($user['nome']) ?></h2>
    <div class="card p-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="row g-3">
                <div class="col-12">
                    <label for="nome" class="form-label">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= $nome_form ?>">
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label">Nome de Usuário *</label>
                    <input type="text" id="username" name="username" class="form-control" required value="<?= $username_form ?>">
                    <small class="text-muted">Será usado para o login.</small>
                </div>
                <div class="col-md-6">
                    <label for="new_password" class="form-label">Nova Senha</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                    <small class="text-muted">Deixe em branco para manter a senha atual.</small>
                </div>
                <div class="col-12">
                    <label for="permissao" class="form-label">Permissão *</label>
                    <select id="permissao" name="permissao" class="form-select" required>
                        <option value="funcionario" <?= $permissao_form == 'funcionario' ? 'selected' : '' ?>>Funcionário (Cria, Lê, Atualiza)</option>
                        <option value="admin" <?= $permissao_form == 'admin' ? 'selected' : '' ?>>Administrador (Total)</option>
                    </select>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Atualizar Usuário</button>
                    <a href="usuario_read.php" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>