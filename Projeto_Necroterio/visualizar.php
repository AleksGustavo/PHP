<?php
require_once 'db/conexao.php';

$corpo = null;
$mensagem = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql_select = "SELECT * FROM corpos WHERE id = ?";
    $stmt_select = $pdo->prepare($sql_select); 
    
    $stmt_select->execute([$id]);
    
    $corpo = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if (!$corpo) {
        $mensagem = "<div class='alert alert-warning'>Registro não encontrado ou ID inválido.</div>";
    }
} else {
    $mensagem = "<div class='alert alert-danger'>ID do registro não fornecido.</div>";
}

function formatar_data($data) {
    return $data ? date('d/m/Y H:i', strtotime($data)) : 'N/A';
}
function formatar_data_simples($data) {
    return $data && $data != '0000-00-00' ? date('d/m/Y', strtotime($data)) : 'N/A';
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Necrotério | Detalhes do Corpo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f2f3; }
        .container { margin-top: 50px; max-width: 700px; }
        .card-header-necro { background-color: #343a40; color: white; }
        .detail-label { font-weight: bold; color: #495057; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow">
        <div class="card-header card-header-necro">
            <h1 class="h5 mb-0">Detalhes do Registro de Corpo</h1>
        </div>
        <div class="card-body">
            <?php if ($mensagem): ?>
                <?php echo $mensagem; ?>
                <a href="index.php" class="btn btn-secondary mt-3">Voltar para a Lista</a>
            <?php elseif ($corpo): ?>
                
                <h2 class="h4 mb-4 text-center"><?php echo htmlspecialchars($corpo['nome']); ?></h2>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="detail-label mb-0">ID do Registro:</p>
                        <p><?php echo $corpo['id']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="detail-label mb-0">Localização (Gaveta):</p>
                        <p><span class="badge bg-secondary fs-6"><?php echo htmlspecialchars($corpo['localizacao_gaveta'] ?: 'N/A'); ?></span></p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="detail-label mb-0">Data de Nascimento:</p>
                        <p><?php echo formatar_data_simples($corpo['data_nascimento']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="detail-label mb-0">Data do Óbito:</p>
                        <p><?php echo formatar_data_simples($corpo['data_obito']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="detail-label mb-0">Data de Entrada:</p>
                        <p><?php echo formatar_data($corpo['data_entrada']); ?></p>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="detail-label mb-0">Causa da Morte:</p>
                    <p><?php echo htmlspecialchars($corpo['causa_morte'] ?: 'Não informada'); ?></p>
                </div>

                <div class="mb-4">
                    <p class="detail-label mb-0">Observações:</p>
                    <div class="alert alert-light border">
                        <?php echo nl2br(htmlspecialchars($corpo['observacoes'] ?: 'Nenhuma observação registrada.')); ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-secondary me-2">Voltar para a Lista</a>
                    <a href="editar.php?id=<?php echo $corpo['id']; ?>" class="btn btn-primary">Editar Registro</a>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>