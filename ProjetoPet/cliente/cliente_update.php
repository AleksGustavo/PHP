<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Mensagem de erro para validação
$error = '';

// Verifica se o ID do cliente foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: cliente_read.php");
    exit();
}

$id = $_GET['id'];

// 1. Busca os dados do cliente no banco
$stmt = $pdo->prepare("SELECT * FROM cliente WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    header("Location: cliente_read.php");
    exit();
}

// 2. Processa o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $cpf = trim($_POST['cpf']); // NOVO CAMPO
    $complemento = trim($_POST['complemento']); // NOVO CAMPO
    $celular = trim($_POST['celular']);

    if (empty($nome) || empty($sobrenome) || empty($cpf) || empty($celular)) {
        $error = "Por favor, preencha todos os campos obrigatórios (*).";
    } else {
        try {
            // Verificação de CPF UNICO (apenas se for diferente do CPF atual do cliente)
            if ($cpf !== $cliente['cpf']) {
                $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE cpf = ? AND id != ?");
                $stmt_check->execute([$cpf, $id]);
                if ($stmt_check->fetchColumn() > 0) {
                    $error = "Erro: O CPF informado já está cadastrado em outro cliente.";
                }
            }

            if (empty($error)) {
                // CORREÇÃO da QUERY: Inserido 'cpf' e 'complemento'
                $sql = "UPDATE cliente SET nome = ?, sobrenome = ?, cpf = ?, cep = ?, rua = ?, numero = ?, bairro = ?, complemento = ?, data_nascimento = ?, sexo = ?, celular = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $nome,
                    $sobrenome,
                    $cpf, // NOVO CAMPO
                    $_POST['cep'],
                    $_POST['rua'],
                    $_POST['numero'],
                    $_POST['bairro'],
                    $complemento, // NOVO CAMPO
                    $_POST['data_nascimento'],
                    $_POST['sexo'],
                    $celular,
                    $id
                ]);

                header("Location: cliente_read.php?atualizacao_sucesso=true");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Erro ao atualizar cliente: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="../iniciar.php">Pet&Pet</a>
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
    <h2>Editar Cliente</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="sobrenome" class="form-label">Sobrenome *</label>
                    <input type="text" id="sobrenome" name="sobrenome" class="form-control" value="<?= htmlspecialchars($cliente['sobrenome'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="cpf" class="form-label">CPF *</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>" required maxlength="14" placeholder="000.000.000-00">
                </div>

                <hr class="mt-4">
                <h5 class="mb-3">Endereço</h5>

                <div class="col-md-3">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" id="cep" name="cep" class="form-control" value="<?= htmlspecialchars($cliente['cep'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="rua" class="form-label">Rua *</label>
                    <input type="text" id="rua" name="rua" class="form-control" value="<?= htmlspecialchars($cliente['rua'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="numero" class="form-label">Número *</label>
                    <input type="text" id="numero" name="numero" class="form-control" value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" value="<?= htmlspecialchars($cliente['bairro'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" id="complemento" name="complemento" class="form-control" value="<?= htmlspecialchars($cliente['complemento'] ?? '') ?>">
                </div>

                <hr class="mt-4">
                <h5 class="mb-3">Outros Dados</h5>

                <div class="col-md-4">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($cliente['data_nascimento'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select id="sexo" name="sexo" class="form-select" required>
                        <option value="M" <?= ($cliente['sexo'] ?? '') == 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= ($cliente['sexo'] ?? '') == 'F' ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= ($cliente['sexo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="celular" class="form-label">Celular *</label>
                    <input type="text" id="celular" name="celular" class="form-control" value="<?= htmlspecialchars($cliente['celular'] ?? '') ?>" required>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Atualizar Cliente</button>
                    <a href="cliente_read.php" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>