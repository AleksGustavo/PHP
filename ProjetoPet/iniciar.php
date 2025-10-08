<?php
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Petshop - Painel de Gestão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .welcome-card {
            background-color: #fff;
            padding: 4rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .welcome-card h1 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 2.5em;
        }

        .welcome-card p {
            font-size: 1.1em;
            color: #666;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">Pet&Pet</a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../cliente/cliente_read.php">Clientes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../pet/pet_read.php">Pets</a>
                        </li>

                        <?php if (is_admin()):
                        ?>
                            <li class="nav-item">
                                <a class="nav-link" href="usuario/usuario_read.php">Usuários</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">Sair</a>
                        </li>
                    </ul>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu de Gestão</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                    <a class="nav-link" href="venda/venda_create.php">Realizar Venda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cliente/cliente_read.php">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="produto/produto_read.php">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container d-flex justify-content-center align-items-center" style="height: calc(100vh - 56px);">
        <div class="welcome-card">
            <h1>Bem-vindo ao seu Sistema de Gestão.</h1>
            <p>Utilize os botões abaixo para gerenciar os cadastros de clientes e produtos, ou inicie uma nova venda.</p>
            <div class="action-buttons">
                <a href="cliente/cliente_read.php" class="btn btn-primary btn-lg">Gerenciar Clientes</a>
                <a href="produto/produto_read.php" class="btn btn-secondary btn-lg">Gerenciar Produtos</a>
                <a href="venda/venda_create.php" class="btn btn-success btn-lg">Realizar Venda</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>