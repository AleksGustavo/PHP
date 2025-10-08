<?php
require_once 'db/conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome               = trim($_POST['nome']);
    $data_nascimento    = $_POST['data_nascimento'];
    $data_obito         = $_POST['data_obito'];
    $causa_morte        = trim($_POST['causa_morte']);
    $localizacao_gaveta = trim($_POST['localizacao_gaveta']);
    $observacoes        = trim($_POST['observacoes']);

    if (empty($nome)) {
        $mensagem = "<div class='alert alert-danger'>O campo Nome é obrigatório.</div>";
    } else {
        $data_entrada = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO corpos (nome, data_nascimento, data_obito, causa_morte, data_entrada, localizacao_gaveta, observacoes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql); 
        
        $parametros = [
            $nome, 
            $data_nascimento, 
            $data_obito, 
            $causa_morte, 
            $data_entrada, 
            $localizacao_gaveta, 
            $observacoes
        ];
        if ($stmt->execute($parametros)) { 
            header("Location: index.php?status=cadastro_sucesso");
            exit();
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar.</div>";
        }

    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Necrotério | Novo Registro</title>
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
            <h1 class="h5 mb-0">Cadastrar Novo Corpo</h1>
        </div>
        <div class="card-body">
            <?php echo $mensagem; ?>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="nome" class="form-label">Nome do Falecido (Obrigatório)</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>

                    <div class="col-md-6">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
                    </div>

                    <div class="col-md-6">
                        <label for="data_obito" class="form-label">Data do Óbito</label>
                        <input type="date" class="form-control" id="data_obito" name="data_obito">
                    </div>
                    
                    <div class="col-md-8">
                        <label for="causa_morte" class="form-label">Causa da Morte</label>
                        <input type="text" class="form-control" id="causa_morte" name="causa_morte">
                    </div>

                    <div class="col-md-4">
                        <label for="localizacao_gaveta" class="form-label">Localização (Gaveta)</label>
                        <input type="text" class="form-control" id="localizacao_gaveta" name="localizacao_gaveta">
                    </div>

                    <div class="col-12">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success me-2">Salvar Registro</button>
                        <a href="index.php" class="btn btn-secondary">Voltar para a Lista</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$pdo = null;
?>