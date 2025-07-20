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
                <th>Variação</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $produtosAgrupados = [];
            foreach ($produtos as $p) {
                $produtosAgrupados[$p['id']]['dados'] = $p;
                $produtosAgrupados[$p['id']]['variacoes'][] = [
                    'variacao' => $p['variacao'],
                    'quantidade' => $p['quantidade']
                ];
            }

            foreach ($produtosAgrupados as $id => $produto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produto['dados']['nome']); ?></td>
                    <td>R$ <?php echo number_format($produto['dados']['preco'], 2, ',', '.'); ?></td>
                    <td colspan="2">
                        <?php foreach ($produto['variacoes'] as $v): ?>
                            <?php echo htmlspecialchars($v['variacao']); ?>: <?php echo $v['quantidade']; ?> unid.<br>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editarProduto(<?php echo $id; ?>)">Editar</button>
                        <button class="btn btn-success btn-sm">Comprar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
</script>