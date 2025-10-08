<?php
require_once 'db/conexao.php';

$sql = "SELECT id, nome, data_entrada, localizacao_gaveta FROM corpos ORDER BY data_entrada DESC";
$resultado = $pdo->query($sql);

$corpos = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Necrotério | Lista de Corpos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 50px; }
        .card-header-necro { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm">
        <div class="card-header card-header-necro">
            <h1 class="h4 mb-0">Gerenciamento de Registros de Corpos</h1>
        </div>
        <div class="card-body">
            <a href="criar.php" class="btn btn-success mb-3">
                <i class="bi bi-plus-circle-fill"></i> Novo Registro
            </a>
            
            <?php if (count($corpos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nome do Falecido</th>
                                <th>Data de Entrada</th>
                                <th>Localização (Gaveta)</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($corpos as $row):?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['data_entrada'])); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['localizacao_gaveta'] ?: 'N/A'); ?></span></td>
                                <td>
                                    <a href="visualizar.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info me-2 text-white">Visualizar</a>
                                    <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary me-2">Editar</a>
                                    <a href="deletar.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja DELETAR este registro? Esta ação é irreversível.');">Excluir</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    Nenhum corpo registrado ainda.
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