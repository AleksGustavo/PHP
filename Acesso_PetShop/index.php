<?php

// Inicia a sessão no topo da página.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário JÁ ESTÁ logado. Se sim, redireciona para o painel.
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    header("Location: dashboard.php");
    exit();
}

// Variáveis para armazenar mensagens de feedback.
$mensagem_erro = "";
$mensagem_sucesso = "";

// Verifica se o formulário foi submetid.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recebe e limpa os dados de entrada.
    $email = htmlspecialchars(trim($_POST['email']));
    $senha = htmlspecialchars(trim($_POST['senha']));
    
    // Simulação de banco de dados (Apenas um usuário válido).
    $usuario_valido = "admin@petshop.com";
    $senha_valida = "123456"; // Em um sistema real, usaria-se hash (password_hash).
    $nome_usuario = "Gerente PetShop";

    // Verifica as credenciais.
    if ($email === $usuario_valido && $senha === $senha_valida) {
        
        // Login bem-sucedido: Armazena dados essenciais na sessão.
        $_SESSION['usuario_logado'] = true;
        $_SESSION['nome_usuario'] = $nome_usuario;
        $_SESSION['email_usuario'] = $email;
        $_SESSION['perfil'] = "Administrador";

        // Redireciona para a página restrita.
        header("Location: dashboard.php");
        exit();
        
    } else {
        // Login falhou.
        $mensagem_erro = "Erro de Login: E-mail ou senha inválidos. Tente novamente.";
    }
}

// Verifica se há parâmetros de erro ou sucesso na URL (após redirecionamento).
if (isset($_GET['erro'])) {
    if ($_GET['erro'] === 'restrito') {
        $mensagem_erro = "Acesso Negado. Você precisa fazer login para acessar o Painel.";
    } elseif ($_GET['erro'] === 'expirou') {
        $mensagem_erro = "Sessão expirada por inatividade. Faça login novamente.";
    }
}

if (isset($_GET['logout']) && $_GET['logout'] === 'sucesso') {
    $mensagem_sucesso = "Logout realizado com sucesso. Volte sempre!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>PetShop | Login</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>🐾 PetShop Feliz</h1>
        <nav>
            <a href="index.php">Login</a>
            </nav>
    </header>

    <main>
        <div class="container">
            <h2>Acesso ao Painel</h2>

            <?php if (!empty($mensagem_erro)): ?>
                <div class="mensagem-erro"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="mensagem-sucesso"><?php echo $mensagem_sucesso; ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="text" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn-caramelo">Entrar</button>
            </form>
        </div>
    </main>
</body>
</html>