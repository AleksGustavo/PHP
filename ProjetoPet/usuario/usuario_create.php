<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Restrição: Apenas administradores podem acessar esta página
if (!is_admin()) {
    header("Location: ../iniciar.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $permissao = $_POST['permissao'];

    if (empty($nome) || empty($username) || empty($password)) {
        $error = "Todos os campos obrigatórios devem ser preenchidos.";
    } elseif (!in_array($permissao, ['admin', 'funcionario'])) {
        $error = "Permissão inválida.";
    } else {
        // Verifica se o username já existe
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE username = ?");
        $stmt_check->execute([$username]);
        if ($stmt_check->fetchColumn() > 0) {
            $error = "Nome de usuário já existe. Escolha outro.";
        } else {
            // Hash da senha (segurança)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuario (nome, username, senha, permissao) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            try {
                $stmt->execute([$nome, $username, $hashed_password, $permissao]);
                header("Location: usuario_read.php?sucesso=cadastro");
                exit();
            } catch (PDOException $e) {
                $error = "Erro ao cadastrar usuário. Tente novamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">...</nav>
<div class="container mt-5">
    <h2>Cadastrar Novo Usuário</h2>
    <div class="card p-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="row g-3">
                <div class="col-12">
                    <label for="nome" class="form-label">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label">Nome de Usuário *</label>
                    <input type="text" id="username" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <small class="text-muted">Será usado para o login.</small>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Senha *</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="col-12">
                    <label for="permissao" class="form-label">Permissão *</label>
                    <select id="permissao" name="permissao" class="form-select" required>
                        <option value="funcionario" <?= ($_POST['permissao'] ?? 'funcionario') == 'funcionario' ? 'selected' : '' ?>>Funcionário (Cria, Lê, Atualiza)</option>
                        <option value="admin" <?= ($_POST['permissao'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrador (Total)</option>
                    </select>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Salvar Usuário</button>
                    <a href="usuario_read.php" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>