<?php
$pageTitle = 'Produtos';
require_once '../app/Views/layouts/header.php';
?>

<div class="container mt-5">
    <h2>Gerenciamento de Produtos</h2>

    <form action="/produtos/salvar" method="POST" class="mb-5">
        <input type="hidden" name="id" id="produtoId">
        <div class="row">
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="col-md-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
            </div>
        </div>
        <h4 class="mt-4">Variações e Estoque</h4>
        <div id="variacoesContainer">
        </div>
        <button type="button" class="btn btn-secondary btn-sm" id="addVariacao">+ Adicionar Variação</button>
        <button type="submit" class="btn btn-primary mt-3">Salvar Produto</button>
        <button type="button" class="btn btn-light mt-3" id="cancelarEdicao" style="display: none;">Cancelar
            Edição</button>
    </form>

    <h3>Produtos Cadastrados</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Variações em Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $produtosAgrupados = [];
            foreach ($produtos as $p) {
                $produtosAgrupados[$p['id']]['dados'] = ['id' => $p['id'], 'nome' => $p['nome'], 'preco' => $p['preco']];

                if (isset($p['estoque_id']) && $p['estoque_id'] !== null) {
                    $produtosAgrupados[$p['id']]['variacoes'][] = [
                        'estoque_id' => $p['estoque_id'],
                        'variacao' => $p['variacao'],
                        'quantidade' => $p['quantidade']
                    ];
                } else {
                    if (!isset($produtosAgrupados[$p['id']]['variacoes'])) {
                        $produtosAgrupados[$p['id']]['variacoes'] = [];
                    }
                }
            }

            foreach ($produtosAgrupados as $id => $produto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produto['dados']['nome']); ?></td>
                    <td>R$ <?php echo number_format($produto['dados']['preco'], 2, ',', '.'); ?></td>
                    <td>
                        <?php foreach ($produto['variacoes'] as $v): ?>
                            <small><?php echo htmlspecialchars($v['variacao']); ?>: <?php echo $v['quantidade']; ?>
                                unid.</small><br>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editarProduto(<?php echo $id; ?>)">Editar</button>
                        <button class="btn btn-success btn-sm"
                            onclick='abrirModalCompra(<?php echo json_encode($produto); ?>)'>Comprar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="compraModal" tabindex="-1" aria-labelledby="compraModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compraModalLabel">Adicionar ao Carrinho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="modalProdutoNome"></h6>
                <form id="formAdicionarCarrinho">
                    <div class="mb-3">
                        <label for="variacaoSelect" class="form-label">Escolha a Variação:</label>
                        <select class="form-select" id="variacaoSelect" name="estoque_id" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="quantidadeInput" class="form-label">Quantidade:</label>
                        <input type="number" class="form-control" id="quantidadeInput" name="quantidade" value="1"
                            min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layouts/footer.php'; ?>

<script>
    const variacoesContainer = document.getElementById('variacoesContainer');
    const addVariacaoBtn = document.getElementById('addVariacao');

    const addVariacaoField = (variacao = {
        nome: '',
        estoque: ''
    }) => {
        const index = variacoesContainer.querySelectorAll('.row').length;
        const novaRow = document.createElement('div');
        novaRow.classList.add('row', 'mb-2');
        novaRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="variacoes[${index}][nome]"
                    placeholder="Ex: Cor Azul, Tamanho P" value="${variacao.nome}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="variacoes[${index}][estoque]" placeholder="Quantidade"
                    required value="${variacao.estoque}">
            </div>
        `;
        variacoesContainer.appendChild(novaRow);
    };

    addVariacaoBtn.addEventListener('click', () => addVariacaoField());

    addVariacaoField();

    function editarProduto(id) {
        fetch(`/produtos/editar?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('produtoId').value = data.id;
                document.getElementById('nome').value = data.nome;
                document.getElementById('preco').value = data.preco;

                variacoesContainer.innerHTML = '';
                if (data.variacoes && data.variacoes.length > 0) {
                    data.variacoes.forEach(v => {
                        addVariacaoField({
                            nome: v.variacao,
                            estoque: v.quantidade
                        });
                    });
                } else {
                    addVariacaoField();
                }

                document.getElementById('cancelarEdicao').style.display = 'inline-block';
                window.scrollTo(0, 0);
            });
    }

    document.getElementById('cancelarEdicao').addEventListener('click', () => {
        document.querySelector('form').reset();
        document.getElementById('produtoId').value = '';
        variacoesContainer.innerHTML = '';
        addVariacaoField();
        document.getElementById('cancelarEdicao').style.display = 'none';
    });

    const compraModal = new bootstrap.Modal(document.getElementById('compraModal'));
    const modalProdutoNome = document.getElementById('modalProdutoNome');
    const variacaoSelect = document.getElementById('variacaoSelect');
    const quantidadeInput = document.getElementById('quantidadeInput');

    function abrirModalCompra(produto) {
        modalProdutoNome.textContent = produto.dados.nome;
        variacaoSelect.innerHTML = '';

        produto.variacoes.forEach(v => {
            if (v.quantidade > 0) {
                const option = document.createElement('option');
                option.value = v.estoque_id;
                option.textContent = `${v.variacao} (Estoque: ${v.quantidade})`;
                variacaoSelect.appendChild(option);
            }
        });

        compraModal.show();
    }

    document.getElementById('formAdicionarCarrinho').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/carrinho/adicionar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    document.getElementById('cart-count').textContent = data
                        .total_itens;
                    compraModal.hide();
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro na comunicação com o servidor.');
            });
    });
</script>