<?php

class PedidoController
{
    public function adicionarAoCarrinho()
    {
        $produtoId = $_POST['produto_id'] ?? null;
        $quantidade = $_POST['quantidade'] ?? 1;

        $produtoModel = new Produto();
        $produto = $produtoModel->find($produtoId);
        if (!$produto) {
            echo "Produto não encontrado.";
            return;
        }
        if ($produto['quantidade'] < $quantidade) {
            echo "Quantidade solicitada não disponível em estoque.";
            return;
        }

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        if (isset($_SESSION['carrinho'][$produtoId])) {
            $_SESSION['carrinho'][$produtoId]['quantidade'] += $quantidade;
        } else {
            $produtoModel = new Produto();
            $produto = $produtoModel->find($produtoId);
            $_SESSION['carrinho'][$produtoId] = [
                'id' => $produto['id'],
                'nome' => $produto['nome'],
                'preco' => $produto['preco'],
                'quantidade' => $quantidade,
            ];
        }

        $this->recalcularCarrinho();

        echo json_encode([
            'status' => 'success',
            'carrinho' => $_SESSION['carrinho']
        ]);
    }

    private function recalcularCarrinho()
    {
        $subtotal = 0;
        if (isset($_SESSION['carrinho'])) {
            foreach ($_SESSION['carrinho'] as $item) {
                $subtotal += $item['preco'] * $item['quantidade'];
            }
        }

        $frete = 20.00;

        if ($subtotal > 200.00) {
            $frete = 0.00;
        } else if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            $frete = 15.00;
        }

        $_SESSION['carrinho_totais'] = [
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $subtotal + $frete
        ];
    }
}
