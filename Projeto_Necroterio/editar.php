<?php
require_once 'db/conexao.php';

$mensagem = '';
$corpo = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql_select = "SELECT * FROM corpos WHERE id = ?";
    $stmt_select = $pdo->prepare($sql_select); 
    
    $stmt_select->execute([$id]);
    
    $corpo = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if (!$corpo) {
        $mensagem = "<div class='alert alert-warning'>Registro não encontrado.</div>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_update          = intval($_POST['id']);
    $nome               = trim($_POST['nome']);
    $data_nascimento    = $_POST['data_nascimento'];
    $data_obito         = $_POST['data_obito'];
    $causa_morte        = trim($_POST['causa_morte']);
    $localizacao_gaveta = trim($_POST['localizacao_gaveta']);
    $observacoes        = trim($_POST['observacoes']);

    if (empty($nome)) {
        $mensagem = "<div class='alert alert-danger'>O campo Nome é obrigatório.</div>";
    } else {
        $sql_update = "UPDATE corpos SET 
                        nome = ?, 
                        data_nascimento = ?, 
                        data_obito = ?, 
                        causa_morte = ?, 
                        localizacao_gaveta = ?, 
                        observacoes = ? 
                        WHERE id = ?";

        $stmt_update = $pdo->prepare($sql_update);
        
        $parametros = [
            $nome, 
            $data_nascimento, 
            $data_obito, 
            $causa_morte, 
            $localizacao_gaveta, 
            $observacoes, 
            $id_update
        ];

        if ($stmt_update->execute($parametros)) {
            header("Location: index.php?status=edicao_sucesso");
            exit();
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao atualizar.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Necrotério | Editar Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f2f3; }
        .container { margin-top: 50px; max-width: 800px; }
        .card-header-necro { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow">
        <div class="card-header card-header-necro">
            <h1 class="h5 mb-0">Editar Registro de Corpo</h1>
        </div>
        <div class="card-body">
            <?php echo $mensagem; ?>
            
            <?php if ($corpo): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $corpo['id']; ?>">
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="nome" class="form-label">Nome do Falecido (Obrigatório)</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($corpo['nome']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?php echo $corpo['data_nascimento']; ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="data_obito" class="form-label">Data do Óbito</label>
                        <input type="date" class="form-control" id="data_obito" name="data_obito" value="<?php echo $corpo['data_obito']; ?>">
                    </div>
                    
                    <div class="col-md-8">
                        <label for="causa_morte" class="form-label">Causa da Morte</label>
                        <input type="text" class="form-control" id="causa_morte" name="causa_morte" value="<?php echo htmlspecialchars($corpo['causa_morte']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="localizacao_gaveta" class="form-label">Localização (Gaveta)</label>
                        <input type="text" class="form-control" id="localizacao_gaveta" name="localizacao_gaveta" value="<?php echo htmlspecialchars($corpo['localizacao_gaveta']); ?>">
                    </div>

                    <div class="col-12">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($corpo['observacoes']); ?></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary me-2">Atualizar Registro</button>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
            <?php else: ?>
                <div class="text-center">
                    <a href="index.php" class="btn btn-secondary">Voltar para a Lista</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$pdo = null;
?>