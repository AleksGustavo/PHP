<?php
// dashboard.php

// Inclui o arquivo de verificação de sessão. Se o usuário não estiver logado,
// ele será redirecionado para index.php.
include 'verificar_sessao.php';

// As variáveis de sessão estão seguras e disponíveis a partir daqui.
$nome = $_SESSION['nome_usuario']; 
$perfil = $_SESSION['perfil']; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>PetShop | Painel Administrativo</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>🐾 Painel do PetShop</h1>
        <nav>
            <span>Bem-vindo(a), <?php echo htmlspecialchars($nome); ?>! (<?php echo htmlspecialchars($perfil); ?>)</span>
            
            <a href="logout.php" class="btn-logout">Sair</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Gerenciamento de Serviços e Produtos</h2>
            
            <p>Esta é a área restrita do sistema, acessível apenas por usuários autenticados (Logados).</p>
            
            <section style="text-align: left; margin-top: 30px;">
                <h3>Status da Sessão:</h3>
                <ul>
                    <li>Usuário: <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?></li>
                    <li>E-mail: <?php echo htmlspecialchars($_SESSION['email_usuario']); ?></li>
                    <li>Perfil: <?php echo htmlspecialchars($_SESSION['perfil']); ?></li>
                </ul>
            </section>
        </div>
    </main>
</body>
</html>