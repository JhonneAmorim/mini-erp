<?php
$pageTitle = 'Carrinho de Compras';
require_once '../app/Views/layouts/header.php';
?>

<div class="container mt-5">
    <h2>Carrinho de Compras</h2>

    <?php if (empty($carrinho)): ?>
        <div class="alert alert-info">
            Seu carrinho está vazio. Volte para a <a href="/produtos">página de produtos</a> para começar a comprar.
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <h4>Itens do Pedido</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>Preço</th>
                            <th>Qtd.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrinho as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                <td><?php echo htmlspecialchars($item['variacao']); ?></td>
                                <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                <td><?php echo $item['quantidade']; ?></td>
                                <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <hr>
                <div class="text-end">
                    <p>Subtotal: <strong>R$ <?php echo number_format($totais['subtotal'], 2, ',', '.'); ?></strong></p>
                    <p>Frete: <strong>R$ <?php echo number_format($totais['frete'], 2, ',', '.'); ?></strong></p>
                    <h4>Total: <strong>R$ <?php echo number_format($totais['total'], 2, ',', '.'); ?></strong></h4>
                </div>
            </div>

            <div class="col-md-4">
                <h4>Finalizar Compra</h4>
                <form action="/finalizar-pedido" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="cep" name="cep" required>
                    </div>
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço Completo</label>
                        <input type="text" class="form-control" id="endereco" name="endereco"
                            placeholder="Rua, Número, Bairro, Cidade - Estado" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Finalizar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../app/Views/layouts/footer.php'; ?>

<script>
    document.getElementById('cep').addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        const enderecoInput = document.getElementById('endereco');
                        enderecoInput.value =
                            `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                    }
                })
                .catch(error => console.error('Erro ao buscar CEP:', error));
        }
    });
</script>