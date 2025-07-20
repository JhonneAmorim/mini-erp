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
                <div class="card bg-light p-3 mb-4">
                    <form id="formAplicarCupom">
                        <label for="codigo_cupom" class="form-label"><strong>Tem um cupom de desconto?</strong></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="codigo_cupom" name="codigo_cupom"
                                placeholder="Digite o código aqui">
                            <button class="btn btn-secondary" type="submit">Aplicar</button>
                        </div>
                    </form>
                    <div id="cupomMessage" class="mt-2"></div>
                </div>

                <div class="text-end">
                    <p>Subtotal: <strong id="subtotalValor">R$
                            <?php echo number_format($totais['subtotal'], 2, ',', '.'); ?></strong></p>
                    <p id="descontoLinha"
                        style="display: <?php echo ($totais['desconto'] ?? 0) > 0 ? 'block' : 'none'; ?>;">
                        Desconto: <strong id="descontoValor" class="text-success">- R$
                            <?php echo number_format($totais['desconto'] ?? 0, 2, ',', '.'); ?></strong>
                    </p>
                    <p>Frete: <strong id="freteValor">R$
                            <?php echo number_format($totais['frete'], 2, ',', '.'); ?></strong></p>
                    <hr>
                    <h4>Total: <strong id="totalValor">R$
                            <?php echo number_format($totais['total'], 2, ',', '.'); ?></strong></h4>
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

    document.getElementById('formAplicarCupom').addEventListener('submit', function(e) {
        e.preventDefault();
        const cupomMessage = document.getElementById('cupomMessage');
        const formData = new FormData(this);

        fetch('/carrinho/aplicar-cupom', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cupomMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    // Atualiza os totais na tela
                    atualizarTotais(data.totais);
                } else {
                    cupomMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                cupomMessage.innerHTML = `<div class="alert alert-danger">Ocorreu um erro.</div>`;
            });
    });

    function atualizarTotais(totais) {
        const descontoLinha = document.getElementById('descontoLinha');

        document.getElementById('subtotalValor').textContent = 'R$ ' + totais.subtotal.toFixed(2).replace('.', ',');
        document.getElementById('freteValor').textContent = 'R$ ' + totais.frete.toFixed(2).replace('.', ',');
        document.getElementById('totalValor').textContent = 'R$ ' + totais.total.toFixed(2).replace('.', ',');

        if (totais.desconto > 0) {
            document.getElementById('descontoValor').textContent = '- R$ ' + totais.desconto.toFixed(2).replace('.', ',');
            descontoLinha.style.display = 'block';
        } else {
            descontoLinha.style.display = 'none';
        }
    }
</script>