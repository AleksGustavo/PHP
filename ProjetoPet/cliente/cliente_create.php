<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

$error = '';
$success = '';

// LÓGICA DE INSERÇÃO NO BANCO DE DADOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Coleta e validação básica dos dados obrigatórios
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $cpf = trim($_POST['cpf']); // Novo campo
    $cep = trim($_POST['cep']);
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $celular = trim($_POST['celular']);

    if (empty($nome) || empty($sobrenome) || empty($cpf) || empty($cep) || empty($celular)) {
        $error = "Por favor, preencha todos os campos obrigatórios (marcados com *).";
    } else {
        try {
            // 2. Verifica se o CPF já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE cpf = ?");
            $stmt->execute([$cpf]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Erro: O CPF informado já está cadastrado.";
            } else {
                // 3. Insere o novo cliente
                $sql = "INSERT INTO cliente (nome, sobrenome, cpf, cep, rua, numero, bairro, complemento, data_nascimento, sexo, celular) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $nome,
                    $sobrenome,
                    $cpf,
                    $cep,
                    $rua,
                    $numero,
                    $_POST['bairro'],
                    $_POST['complemento'],
                    $_POST['data_nascimento'],
                    $_POST['sexo'],
                    $celular
                ]);

                // Redireciona após o sucesso para a lista de clientes
                header("Location: cliente_read.php?cadastro_sucesso=true");
                exit();
            }

        } catch (PDOException $e) {
            $error = "Erro ao cadastrar cliente: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="../iniciar.php">Petshop - Gestão</a>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Cadastrar Novo Cliente</h2>
        <div>
            <a href="cliente_read.php" class="btn btn-secondary">Voltar à Lista</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="sobrenome" class="form-label">Sobrenome *</label>
                    <input type="text" id="sobrenome" name="sobrenome" class="form-control" required value="<?= htmlspecialchars($_POST['sobrenome'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="cpf" class="form-label">CPF *</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" required value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>" maxlength="14" placeholder="000.000.000-00">
                </div>
                <div class="col-md-4">
                    <label for="celular" class="form-label">Celular *</label>
                    <input type="text" id="celular" name="celular" class="form-control" required value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>" placeholder="(00) 00000-0000">
                </div>

                <hr class="mt-4">
                <h5 class="mb-3">Endereço</h5>

                <div class="col-md-3">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" id="cep" name="cep" class="form-control" required value="<?= htmlspecialchars($_POST['cep'] ?? '') ?>" placeholder="00000-000">
                </div>
                <div class="col-md-6">
                    <label for="rua" class="form-label">Rua *</label>
                    <input type="text" id="rua" name="rua" class="form-control" required value="<?= htmlspecialchars($_POST['rua'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="numero" class="form-label">Número *</label>
                    <input type="text" id="numero" name="numero" class="form-control" required value="<?= htmlspecialchars($_POST['numero'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" value="<?= htmlspecialchars($_POST['bairro'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" id="complemento" name="complemento" class="form-control" value="<?= htmlspecialchars($_POST['complemento'] ?? '') ?>">
                </div>

                <hr class="mt-4">
                <h5 class="mb-3">Outros Dados</h5>

                <div class="col-md-4">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($_POST['data_nascimento'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select id="sexo" name="sexo" class="form-select">
                        <option value="M" <?= ($_POST['sexo'] ?? '') == 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= ($_POST['sexo'] ?? '') == 'F' ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= ($_POST['sexo'] ?? 'Outro') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-success btn-lg">Cadastrar Cliente</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>