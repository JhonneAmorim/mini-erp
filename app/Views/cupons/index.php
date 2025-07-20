<?php
$pageTitle = 'Gerenciar Cupons';
require_once '../app/Views/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-5">
            <h2>Criar Novo Cupom</h2>
            <form action="/cupons/salvar" method="POST" class="card p-3">
                <div class="mb-3">
                    <label for="codigo" class="form-label">Código do Cupom</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_desconto" class="form-label">Tipo de Desconto</label>
                    <select class="form-select" name="tipo_desconto" id="tipo_desconto">
                        <option value="percentual">Percentual (%)</option>
                        <option value="fixo">Fixo (R$)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="valor" class="form-label">Valor</label>
                    <input type="number" step="0.01" class="form-control" id="valor" name="valor" required>
                </div>
                <div class="mb-3">
                    <label for="valor_minimo_pedido" class="form-label">Valor Mínimo do Pedido (R$)</label>
                    <input type="number" step="0.01" class="form-control" id="valor_minimo_pedido"
                        name="valor_minimo_pedido">
                </div>
                <div class="mb-3">
                    <label for="data_validade" class="form-label">Data de Validade</label>
                    <input type="date" class="form-control" id="data_validade" name="data_validade" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" checked>
                    <label class="form-check-label" for="ativo">Ativo</label>
                </div>
                <button type="submit" class="btn btn-primary">Salvar Cupom</button>
            </form>
        </div>

        <div class="col-md-7">
            <h2>Cupons Cadastrados</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Validade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cupons as $cupom): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cupom['codigo']); ?></td>
                            <td><?php echo ucfirst($cupom['tipo_desconto']); ?></td>
                            <td>
                                <?php echo $cupom['tipo_desconto'] == 'percentual' ? $cupom['valor'] . '%' : 'R$ ' . number_format($cupom['valor'], 2, ',', '.'); ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($cupom['data_validade'])); ?></td>
                            <td>
                                <span class="badge <?php echo $cupom['ativo'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $cupom['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layouts/footer.php'; ?>