<?php
session_start();
require_once 'includes/conexao.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_password = $_POST['admin_password'];

    // CORREÇÃO: Busca as credenciais completas do usuário Admin
    // Você precisa de user_id, username e permissao para a sessão
    $stmt = $pdo->prepare("SELECT id, username, senha, permissao FROM usuario WHERE permissao = 'admin' AND username = 'admin' LIMIT 1");
    $stmt->execute();
    $admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin_user && password_verify($admin_password, $admin_user['senha'])) {
        
        // SUCESSO: Autentica o usuário Admin COMPLETAMENTE
        $_SESSION['user_id'] = $admin_user['id'];
        $_SESSION['username'] = $admin_user['username'];
        $_SESSION['permissao'] = $admin_user['permissao'];
        
        // Redireciona para o Painel de Usuários
        header("Location: usuario/usuario_read.php");
        exit();
    } else {
        $error = "Senha de Administrador incorreta.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Acesso Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 login-card">
        <h2 class="text-center mb-4">Acesso Administrativo</h2>
        <p class="text-center text-muted mb-4">Insira a senha do Super Administrador para gerenciar usuários.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="admin_password" class="form-label">Senha do Administrador</label>
                <input type="password" id="admin_password" name="admin_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Acessar Painel</button>
        </form>
        <p class="text-center mt-3">
            <a href="index.php" class="btn btn-link">Voltar ao Login</a>
        </p>
    </div>
</div>
</body>
</html>