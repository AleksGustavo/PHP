<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

$cliente_selecionado = null;
$cliente = null;
$error = '';

// Verifica se um cliente_id foi passado na URL
if (isset($_GET['cliente_id'])) {
    $cliente_selecionado = intval($_GET['cliente_id']);
    
    // Busca APENAS o cliente com o ID passado
    $stmt = $pdo->prepare("SELECT id, nome, sobrenome FROM cliente WHERE id = ?");
    $stmt->execute([$cliente_selecionado]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se o cliente não existir, redireciona de volta
    if (!$cliente) {
        header("Location: ../cliente/cliente_read.php");
        exit();
    }
} else {
    // Se nenhum cliente_id for passado, redireciona de volta
    header("Location: ../cliente/cliente_read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagem_path = null;
    $cliente_id_post = $_POST['cliente_id'];
    
    // Lógica para upload de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $upload_dir = '../uploads/pets/';
        
        // Garante que o diretório exista
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_FILES['imagem']['name'];
        $file_tmp = $_FILES['imagem']['tmp_name'];
        
        // Cria um nome de arquivo único para evitar colisões (ex: 65161d7b383d4.jpg)
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '.' . $extension;
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $imagem_path = $new_file_name; // Salva apenas o nome do arquivo no banco
        } else {
            $error = "Erro ao mover o arquivo de imagem.";
        }
    }
    
    if (empty($error)) {
        // QUERY ATUALIZADA: Incluído o campo 'imagem'
        $sql = "INSERT INTO pet (cliente_id, nome, cor, especie, raca, sexo, castrado, data_nascimento, imagem)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $cliente_id_post, 
            $_POST['nome'], 
            $_POST['cor'], 
            $_POST['especie'],
            $_POST['raca'], 
            $_POST['sexo'], 
            isset($_POST['castrado']) ? 1 : 0, 
            $_POST['data_nascimento'],
            $imagem_path // Novo valor
        ]);
        
        // Redireciona para a lista de pets do cliente recém-cadastrado
        header("Location: pet_read.php?cliente_id=" . $cliente_id_post . "&cadastro_sucesso=true");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Pet</title>
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
    <h2>Cadastrar Pet</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-12">
                    <label for="cliente_id" class="form-label">Dono (Cliente) *</label>
                    <input type="text" class="form-control-plaintext" value="<?= htmlspecialchars($cliente['nome'] . ' ' . $cliente['sobrenome']) ?>" readonly>
                    <input type="hidden" name="cliente_id" value="<?= htmlspecialchars($cliente['id']) ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="cor" class="form-label">Cor</label>
                    <input type="text" id="cor" name="cor" class="form-control">
                </div>
                
                <div class="col-12">
                    <label for="imagem" class="form-label">Foto do Pet</label>
                    <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label for="especie" class="form-label">Espécie</label>
                    <input type="text" id="especie" name="especie" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="raca" class="form-label">Raça</label>
                    <input type="text" id="raca" name="raca" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select id="sexo" name="sexo" class="form-select">
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="castrado" name="castrado" value="1">
                        <label class="form-check-label" for="castrado">Castrado</label>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Salvar Pet</button>
                    <a href="pet_read.php?cliente_id=<?= htmlspecialchars($cliente['id']) ?>" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>