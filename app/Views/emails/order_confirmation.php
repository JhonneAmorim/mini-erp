<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
        }

        .header {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .total {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Obrigado pelo seu pedido!</h2>
        </div>
        <p>Olá, <?php echo htmlspecialchars($pedidoData['cliente_nome']); ?>!</p>
        <p>Recebemos o seu pedido <strong>#<?php echo $pedidoData['pedido_id']; ?></strong> e ele já está sendo
            processado.</p>

        <h3>Detalhes do Pedido</h3>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd.</th>
                    <th>Preço</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itensCarrinho as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nome'] . ' (' . $item['variacao'] . ')'); ?></td>
                        <td><?php echo $item['quantidade']; ?></td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <p>Subtotal: R$ <?php echo number_format($pedidoData['subtotal'], 2, ',', '.'); ?></p>
            <?php if ($pedidoData['desconto'] > 0): ?>
                <p>Desconto: - R$ <?php echo number_format($pedidoData['desconto'], 2, ',', '.'); ?></p>
            <?php endif; ?>
            <p>Frete: R$ <?php echo number_format($pedidoData['frete'], 2, ',', '.'); ?></p>
            <h3>Total: R$ <?php echo number_format($pedidoData['total'], 2, ',', '.'); ?></h3>
        </div>

        <h4>Endereço de Entrega</h4>
        <p><?php echo htmlspecialchars($pedidoData['endereco']); ?></p>
    </div>
</body>

</html>