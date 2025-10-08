<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Restrição: Apenas administradores podem acessar esta página
if (!is_admin()) {
    header("Location: ../iniciar.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

// Busca dados do Admin logado para o título
$stmt_admin = $pdo->prepare("SELECT nome, username FROM usuario WHERE id = ?");
$stmt_admin->execute([$user_id]);
$admin_data = $stmt_admin->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Todos os campos de senha devem ser preenchidos.";
    } elseif ($new_password !== $confirm_password) {
        $error = "A nova senha e a confirmação não coincidem.";
    } else {
        // Hash da nova senha (segurança)
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Atualiza apenas a senha no banco
        $sql = "UPDATE usuario SET senha = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$hashed_password, $user_id]);
        
        // Redireciona para o login com mensagem de sucesso (força o relogin com a nova senha)
        header("Location: ../index.php?senha_sucesso=true");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mudar Minha Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">...</nav>
<div class="container mt-5">
    <h2>Mudar Senha - <?= htmlspecialchars($admin_data['username']) ?></h2>
    <div class="card p-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="new_password" class="form-label">Nova Senha *</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirme a Nova Senha *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Alterar Senha e Sair</button>
                    <a href="usuario_read.php" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>