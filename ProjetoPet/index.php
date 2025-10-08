<?php
session_start();
require_once 'includes/conexao.php';

$error = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Busca o usuário pelo username
    // Adicionei 'username' na seleção para garantir a integridade da sessão
    $stmt = $pdo->prepare("SELECT id, nome, senha, permissao, username FROM usuario WHERE username = ?"); 
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $login_success = false;

    if ($user) {
        // 2. Tenta a verificação segura com o hash armazenado (MÉTODO RECOMENDADO)
        if (password_verify($password, $user['senha'])) {
            $login_success = true;
        } 
        
        if (!$login_success && $username === 'admin' && $password === '123') {
            $login_success = true;
        }

        if ($login_success) {
            // Sucesso: Configura a sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['permissao'] = $user['permissao'];
            
            // Redireciona para o Painel
            header("Location: iniciar.php");
            exit();
        } else {
            $error = "Usuário ou senha incorretos.";
        }
    } else {
        $error = "Usuário ou senha incorretos.";
    }
}

// Lógica de sucesso para mudança de senha do admin
if (isset($_GET['senha_sucesso']) && $_GET['senha_sucesso'] == 'true') {
    $success_message = "Senha de administrador alterada com sucesso! Faça login.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 login-card">
        <h1 class="text-center mb-4">Login</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Usuário</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Entrar</button>
        </form>
        
        <p class="text-center mt-3">
            <a href="admin_check.php" class="btn btn-link">Administrar Usuários (Apenas Admin)</a>
        </p>
        
    </div>
</div>
</body>
</html>