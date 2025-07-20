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
            <div class="row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="variacoes[0][nome]"
                        placeholder="Ex: Cor Azul, Tamanho P">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="variacoes[0][estoque]" placeholder="Quantidade"
                        required>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" id="addVariacao">+ Adicionar Variação</button>
        <button type="submit" class="btn btn-primary mt-3">Salvar Produto</button>
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
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                    <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($produto['variacao']); ?></td>
                    <td><?php echo $produto['quantidade']; ?></td>
                    <td>
                        <button class="btn btn-info btn-sm">Editar</button>
                        <button class="btn btn-success btn-sm">Comprar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../app/Views/layouts/footer.php'; ?>

<script>
    document.getElementById('addVariacao').addEventListener('click', () => {
        const container = document.getElementById('variacoesContainer');
        const index = container.querySelectorAll('.row').length;

        const novaRow = document.createElement('div');
        novaRow.classList.add('row', 'mb-2');
        novaRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" class="form-control" name="variacoes[${index}][nome]"
                placeholder="Ex: Cor Vermelha, Tamanho G">
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" name="variacoes[${index}][estoque]"
                placeholder="Quantidade" required>
        </div>
    `;
        container.appendChild(novaRow);
    });
</script>