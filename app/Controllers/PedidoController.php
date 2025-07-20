<?php

class PedidoController
{

    public function index()
    {
        $pedidoModel = new Pedido();
        $pedidos = $pedidoModel->getAll();
        require_once '../app/Views/pedidos/index.php';
    }

    public function carrinho()
    {
        $carrinho = $_SESSION['carrinho'] ?? [];
        $totais = $_SESSION['carrinho_totais'] ?? ['subtotal' => 0, 'frete' => 0, 'total' => 0];

        require_once '../app/Views/carrinho/index.php';
    }

    public function finalizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $pedidoData = [
            'cliente_nome' => $_POST['nome'],
            'cliente_email' => $_POST['email'],
            'cep' => $_POST['cep'],
            'endereco' => $_POST['endereco'],
            'subtotal' => $_SESSION['carrinho_totais']['subtotal'],
            'frete' => $_SESSION['carrinho_totais']['frete'],
            'total' => $_SESSION['carrinho_totais']['total'],
        ];

        $itensCarrinho = $_SESSION['carrinho'];

        $pedidoModel = new Pedido();
        if ($pedidoModel->create($pedidoData, $itensCarrinho)) {
            unset($_SESSION['carrinho']);
            unset($_SESSION['carrinho_totais']);

            header('Location: /pedidos');
            exit;
        } else {
            header('Location: /carrinho?erro=1');
            exit;
        }
    }

    public function adicionarAoCarrinho()
    {
        header('Content-Type: application/json');

        $estoqueId = $_POST['estoque_id'] ?? null;
        $quantidade = $_POST['quantidade'] ?? 1;

        if (!$estoqueId || $quantidade <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos.']);
            return;
        }

        $produtoModel = new Produto();
        $variacao = $produtoModel->findVariation($estoqueId);

        if (!$variacao) {
            echo json_encode(['status' => 'error', 'message' => 'Produto ou variação não encontrada.']);
            return;
        }

        if ($variacao['quantidade'] < $quantidade) {
            echo json_encode(['status' => 'error', 'message' => 'Quantidade solicitada não disponível em estoque.']);
            return;
        }

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        if (isset($_SESSION['carrinho'][$estoqueId])) {
            if ($variacao['quantidade'] < ($_SESSION['carrinho'][$estoqueId]['quantidade'] + $quantidade)) {
                echo json_encode(['status' => 'error', 'message' => 'Não é possível adicionar mais unidades deste item. Estoque insuficiente.']);
                return;
            }
            $_SESSION['carrinho'][$estoqueId]['quantidade'] += $quantidade;
        } else {
            $_SESSION['carrinho'][$estoqueId] = [
                'produto_id' => $variacao['produto_id'],
                'estoque_id' => $variacao['estoque_id'],
                'nome' => $variacao['nome'],
                'variacao' => $variacao['variacao'],
                'preco' => $variacao['preco'],
                'quantidade' => $quantidade,
            ];
        }

        $this->recalcularCarrinho();

        echo json_encode([
            'status' => 'success',
            'message' => 'Produto adicionado ao carrinho!',
            'total_itens' => count($_SESSION['carrinho'])
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
