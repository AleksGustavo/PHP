<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Verifica se o ID do pet foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: pet_read.php");
    exit();
}

$id = $_GET['id'];

// Busca os dados do pet no banco
$stmt = $pdo->prepare("SELECT * FROM pet WHERE id = ?");
$stmt->execute([$id]);
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pet) {
    header("Location: pet_read.php");
    exit();
}

// Busca todos os clientes para popular o select
$stmt_clientes = $pdo->query("SELECT id, nome, sobrenome FROM cliente ORDER BY nome");
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

// Processa o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagem_atual = $pet['imagem'];
    $imagem_path = $imagem_atual; // Mantém a imagem atual por padrão
    $upload_dir = '../uploads/pets/';
    
    // Lógica para upload e substituição de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        
        // Garante que o diretório exista
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_FILES['imagem']['name'];
        $file_tmp = $_FILES['imagem']['tmp_name'];
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '.' . $extension;
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            // Se o upload for bem-sucedido, define o novo caminho
            $imagem_path = $new_file_name; 
            
            // Exclui o arquivo antigo se ele existir
            if ($imagem_atual && file_exists($upload_dir . $imagem_atual)) {
                unlink($upload_dir . $imagem_atual); 
            }
        }
    }
    
    // QUERY ATUALIZADA: Incluído o campo 'imagem'
    $sql = "UPDATE pet SET cliente_id = ?, nome = ?, cor = ?, especie = ?, raca = ?, sexo = ?, castrado = ?, data_nascimento = ?, imagem = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['cliente_id'], 
        $_POST['nome'], 
        $_POST['cor'], 
        $_POST['especie'],
        $_POST['raca'], 
        $_POST['sexo'], 
        isset($_POST['castrado']) ? 1 : 0, 
        $_POST['data_nascimento'], 
        $imagem_path, // Novo valor (ou o antigo, se não houve upload)
        $id
    ]);
    
    header("Location: pet_read.php?cliente_id=" . $_POST['cliente_id']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Pet</title>
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
                    <a class="nav-link" href="../cliente/cliente_read.php">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pet_read.php">Pets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2>Editar Pet: <?= htmlspecialchars($pet['nome']) ?></h2>
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-12">
                    <label for="cliente_id" class="form-label">Dono (Cliente) *</label>
                    <select id="cliente_id" name="cliente_id" class="form-select" required>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= htmlspecialchars($cliente['id']) ?>" <?= $pet['cliente_id'] == $cliente['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cliente['nome'] . ' ' . $cliente['sobrenome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($pet['nome']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="cor" class="form-label">Cor</label>
                    <input type="text" id="cor" name="cor" class="form-control" value="<?= htmlspecialchars($pet['cor']) ?>">
                </div>
                
                <div class="col-12">
                    <label for="imagem" class="form-label">Foto Atual</label>
                    <?php 
                    $imagem_src = empty($pet['imagem']) ? '../img/placeholder-pet.png' : '../uploads/pets/' . htmlspecialchars($pet['imagem']);
                    ?>
                    <div class="mb-3">
                        <img src="<?= $imagem_src ?>" alt="Foto atual de <?= htmlspecialchars($pet['nome']) ?>" style="max-height: 150px; border: 1px solid #ccc; border-radius: 8px; object-fit: cover;">
                    </div>
                    
                    <label for="imagem" class="form-label">Alterar Foto</label>
                    <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*">
                    <small class="form-text text-muted">Selecione um novo arquivo para substituir a foto atual.</small>
                </div>
                <div class="col-md-6">
                    <label for="especie" class="form-label">Espécie</label>
                    <input type="text" id="especie" name="especie" class="form-control" value="<?= htmlspecialchars($pet['especie']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="raca" class="form-label">Raça</label>
                    <input type="text" id="raca" name="raca" class="form-control" value="<?= htmlspecialchars($pet['raca']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select id="sexo" name="sexo" class="form-select">
                        <option value="M" <?= $pet['sexo'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= $pet['sexo'] == 'F' ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= $pet['sexo'] == 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($pet['data_nascimento']) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="castrado" name="castrado" value="1" <?= $pet['castrado'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="castrado">Castrado</label>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Atualizar Pet</button>
                    <a href="pet_read.php?cliente_id=<?= htmlspecialchars($pet['cliente_id']) ?>" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>