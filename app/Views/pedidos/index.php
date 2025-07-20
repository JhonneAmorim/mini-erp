<?php
$pageTitle = 'Meus Pedidos';
require_once '../app/Views/layouts/header.php';
?>

<div class="container mt-5">
    <h2>Meus Pedidos</h2>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">
            Você ainda não fez nenhum pedido.
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID do Pedido</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td>#<?php echo $pedido['id']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['cliente_nome']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                        <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="badge bg-primary"><?php echo htmlspecialchars(ucfirst($pedido['status'])); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once '../app/Views/layouts/footer.php'; ?>