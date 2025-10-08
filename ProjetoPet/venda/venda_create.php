<?php
require_once '../includes/auth.php';
require_once '../includes/conexao.php';

// Carrega todos os produtos para que o JavaScript possa ter acesso aos dados
$produtos_stmt = $pdo->query("SELECT id, nome, preco, estoque FROM produto ORDER BY nome");
$produtos_data = $produtos_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        $cliente_id = !empty($_POST['cliente_id']) ? intval($_POST['cliente_id']) : null;
        $total = 0;

        $sql_venda = "INSERT INTO venda (cliente_id, total) VALUES (?, 0)";
        $stmt_venda = $pdo->prepare($sql_venda);
        $stmt_venda->execute([$cliente_id]);
        $venda_id = $pdo->lastInsertId();

        if (isset($_POST['produtos']) && is_array($_POST['produtos'])) {
            foreach ($_POST['produtos'] as $item) {
                $produto_id = intval($item['id']);
                $quantidade = intval($item['quantidade']);
                
                if ($quantidade > 0) {
                    $sql_produto = "SELECT preco, estoque FROM produto WHERE id = ?";
                    $stmt_produto = $pdo->prepare($sql_produto);
                    $stmt_produto->execute([$produto_id]);
                    $produto = $stmt_produto->fetch(PDO::FETCH_ASSOC);
    
                    if (!$produto || $produto['estoque'] < $quantidade) {
                        throw new Exception("Estoque insuficiente para o produto ID: {$produto_id}");
                    }
    
                    $subtotal = $produto['preco'] * $quantidade;
                    $total += $subtotal;
    
                    $sql_item = "INSERT INTO venda_item (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
                    $stmt_item = $pdo->prepare($sql_item);
                    $stmt_item->execute([$venda_id, $produto_id, $quantidade, $produto['preco']]);
    
                    $sql_update_estoque = "UPDATE produto SET estoque = estoque - ? WHERE id = ?";
                    $stmt_update_estoque = $pdo->prepare($sql_update_estoque);
                    $stmt_update_estoque->execute([$quantidade, $produto_id]);
                }
            }
        }
        
        $sql_update_total = "UPDATE venda SET total = ? WHERE id = ?";
        $stmt_update_total = $pdo->prepare($sql_update_total);
        $stmt_update_total->execute([$total, $venda_id]);

        $pdo->commit();
        header("Location: venda_read.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao processar a venda: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Realizar Venda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">Petshop - Gestão</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../cliente/cliente_read.php">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../produto/produto_read.php">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Realizar Venda</h2>
    <div class="card p-4">
        <form id="form-venda" method="POST">
            <div class="mb-3">
                <label for="cliente_nome" class="form-label">Cliente</label>
                <input type="text" id="cliente_nome" class="form-control" placeholder="Buscar por nome ou ID..." autocomplete="off">
                <input type="hidden" id="cliente_id" name="cliente_id">
                <div id="cliente-results" class="list-group position-absolute w-100 mt-1" style="z-index: 1000;"></div>
                <small class="form-text text-muted">Deixe em branco para venda sem cliente cadastrado.</small>
            </div>

            <hr>

            <h4>Produtos</h4>
            <div id="lista-produtos" class="mb-3">
                </div>

            <div class="mb-3">
                <button type="button" class="btn btn-outline-primary" id="add-produto">Adicionar Produto</button>
            </div>

            <h3 class="mt-4">Total: R$ <span id="total-venda">0,00</span></h3>

            <button type="submit" class="btn btn-success mt-3">Finalizar Venda</button>
            <a href="../index.php" class="btn btn-secondary mt-3">Cancelar</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clienteNomeInput = document.getElementById('cliente_nome');
        const clienteIdInput = document.getElementById('cliente_id');
        const clienteResultsDiv = document.getElementById('cliente-results');
        const addProdutoBtn = document.getElementById('add-produto');
        const listaProdutosDiv = document.getElementById('lista-produtos');
        const totalVendaSpan = document.getElementById('total-venda');

        let produtosData = <?= json_encode($produtos_data) ?>;
        let itemIndex = 0;

        // Lógica de busca de clientes
        clienteNomeInput.addEventListener('input', debounce(async function() {
            const termo = this.value.trim();
            if (termo.length < 2) {
                clienteResultsDiv.innerHTML = '';
                return;
            }

            const response = await fetch(`venda_search_cliente.php?q=${encodeURIComponent(termo)}`);
            const clientes = await response.json();
            
            clienteResultsDiv.innerHTML = '';
            if (clientes.length > 0) {
                clientes.forEach(cliente => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${cliente.nome} ${cliente.sobrenome} (ID: ${cliente.id})`;
                    item.dataset.id = cliente.id;
                    item.dataset.nome = `${cliente.nome} ${cliente.sobrenome}`;
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        clienteNomeInput.value = item.dataset.nome;
                        clienteIdInput.value = item.dataset.id;
                        clienteResultsDiv.innerHTML = '';
                    });
                    clienteResultsDiv.appendChild(item);
                });
            } else {
                clienteResultsDiv.innerHTML = '<div class="list-group-item text-muted">Nenhum cliente encontrado.</div>';
            }
        }, 300));

        // Lógica de adicionar e remover produtos
        function adicionarProdutoInput() {
            const div = document.createElement('div');
            div.className = 'row g-2 mb-2 produto-item';
            div.innerHTML = `
                <div class="col-md-3">
                    <input type="text" name="produtos[${itemIndex}][id]" class="form-control produto-id" placeholder="ID" autocomplete="off">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control produto-nome" placeholder="Nome do Produto" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <input type="number" name="produtos[${itemIndex}][quantidade]" class="form-control quantidade-input" placeholder="Qtd" min="1" value="1">
                </div>
                <input type="hidden" class="produto-preco">
                <input type="hidden" class="produto-estoque">
                <div class="col-md-1 d-flex align-items-center">
                    <span class="preco-display text-muted">R$ 0,00</span>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-danger remove-produto">&times;</button>
                </div>
                <div class="col-md-12">
                    <small class="form-text text-danger estoque-aviso" style="display:none;"></small>
                </div>
            `;
            listaProdutosDiv.appendChild(div);
            itemIndex++;
        }

        adicionarProdutoInput();

        addProdutoBtn.addEventListener('click', adicionarProdutoInput);

        listaProdutosDiv.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-produto')) {
                e.target.closest('.produto-item').remove();
                calcularTotal();
            }
        });

        listaProdutosDiv.addEventListener('input', function(e) {
            const target = e.target;
            if (target.classList.contains('produto-id') || target.classList.contains('produto-nome')) {
                debounce(async () => {
                    const idInput = target.closest('.produto-item').querySelector('.produto-id');
                    const nomeInput = target.closest('.produto-item').querySelector('.produto-nome');
                    const precoHidden = target.closest('.produto-item').querySelector('.produto-preco');
                    const estoqueHidden = target.closest('.produto-item').querySelector('.produto-estoque');
                    
                    const termo = target.value.trim();
                    if (termo.length === 0) {
                        idInput.value = '';
                        nomeInput.value = '';
                        precoHidden.value = '';
                        estoqueHidden.value = '';
                        calcularTotal();
                        return;
                    }

                    const isId = !isNaN(termo) && termo.length > 0;
                    const queryParam = isId ? `q=${termo}` : `q=${encodeURIComponent(termo)}`;
                    const response = await fetch(`venda_search_produto.php?${queryParam}`);
                    const produto = await response.json();

                    if (produto) {
                        idInput.value = produto.id;
                        nomeInput.value = produto.nome;
                        precoHidden.value = produto.preco;
                        estoqueHidden.value = produto.estoque;
                    } else {
                        // Limpa os campos se o produto não for encontrado, mas mantém o que o usuário digitou
                        if (target.classList.contains('produto-id')) {
                            nomeInput.value = '';
                        } else {
                            idInput.value = '';
                        }
                        precoHidden.value = '';
                        estoqueHidden.value = '';
                    }
                    calcularTotal();
                })();
            } else if (target.classList.contains('quantidade-input')) {
                calcularTotal();
            }
        });

        function calcularTotal() {
            let total = 0;
            document.querySelectorAll('.produto-item').forEach(item => {
                const precoHidden = item.querySelector('.produto-preco');
                const quantidadeInput = item.querySelector('.quantidade-input');
                const estoqueHidden = item.querySelector('.produto-estoque');
                const precoDisplay = item.querySelector('.preco-display');
                const estoqueAviso = item.querySelector('.estoque-aviso');

                const preco = parseFloat(precoHidden.value) || 0;
                const quantidade = parseInt(quantidadeInput.value) || 0;
                const estoque = parseInt(estoqueHidden.value) || 0;

                const subtotal = preco * quantidade;
                precoDisplay.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;

                if (quantidade > estoque) {
                    estoqueAviso.textContent = `Atenção: Apenas ${estoque} em estoque.`;
                    estoqueAviso.style.display = 'block';
                } else {
                    estoqueAviso.style.display = 'none';
                }

                total += subtotal;
            });
            totalVendaSpan.textContent = total.toFixed(2).replace('.', ',');
        }

        // Função utilitária para "debouncing", evitando muitas requisições AJAX
        function debounce(func, timeout = 300) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }
    });
</script>
</body>
</html>