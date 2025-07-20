<?php

require_once '../app/Services/EmailService.php';

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

    public function aplicarCupom()
    {
        header('Content-Type: application/json');
        $codigoCupom = $_POST['codigo_cupom'] ?? '';

        if (empty($codigoCupom)) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor, insira um código de cupom.']);
            return;
        }

        $cupomModel = new Cupom();
        $cupom = $cupomModel->findByCode($codigoCupom);

        if (!$cupom) {
            echo json_encode(['status' => 'error', 'message' => 'Cupom inválido ou expirado.']);
            return;
        }

        $subtotal = $_SESSION['carrinho_totais']['subtotal'] ?? 0;
        if ($subtotal < $cupom['valor_minimo_pedido']) {
            echo json_encode(['status' => 'error', 'message' => 'O valor do pedido não atinge o mínimo necessário para este cupom.']);
            return;
        }

        $_SESSION['cupom_aplicado'] = $cupom;

        $this->recalcularCarrinho();

        echo json_encode([
            'status' => 'success',
            'message' => 'Cupom aplicado com sucesso!',
            'totais' => $_SESSION['carrinho_totais']
        ]);
    }

    public function finalizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $this->recalcularCarrinho();

        $pedidoData = [
            'cliente_nome' => $_POST['nome'],
            'cliente_email' => $_POST['email'],
            'cep' => $_POST['cep'],
            'endereco' => $_POST['endereco'],
            'subtotal' => $_SESSION['carrinho_totais']['subtotal'],
            'frete' => $_SESSION['carrinho_totais']['frete'],
            'desconto' => $_SESSION['carrinho_totais']['desconto'],
            'total' => $_SESSION['carrinho_totais']['total'],
            'cupom_id' => $_SESSION['cupom_aplicado']['id'] ?? null,
        ];

        $itensCarrinho = $_SESSION['carrinho'];

        $pedidoModel = new Pedido();
        $pedidoCriado = $pedidoModel->create($pedidoData, $itensCarrinho);
        if ($pedidoCriado) {
            $pedidoData['pedido_id'] = $pedidoCriado;

            $emailService = new EmailService();
            $emailService->sendOrderConfirmation($pedidoData, $itensCarrinho);

            unset($_SESSION['carrinho']);
            unset($_SESSION['carrinho_totais']);
            unset($_SESSION['cupom_aplicado']);

            header('Location: /pedidos?sucesso=1');
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
        if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
            foreach ($_SESSION['carrinho'] as $item) {
                $subtotal += $item['preco'] * $item['quantidade'];
            }
        } else {
            unset($_SESSION['cupom_aplicado']);
        }

        $desconto = 0;
        if (isset($_SESSION['cupom_aplicado'])) {
            $cupom = $_SESSION['cupom_aplicado'];

            if ($cupom['tipo_desconto'] == 'percentual') {
                $desconto = ($subtotal * $cupom['valor']) / 100;
            } else {
                $desconto = $cupom['valor'];
            }
            if ($desconto > $subtotal) {
                $desconto = $subtotal;
            }
        }

        $frete = 20.00;
        if (($subtotal - $desconto) > 200.00) {
            $frete = 0.00;
        } else if (($subtotal - $desconto) >= 52.00 && ($subtotal - $desconto) <= 166.59) {
            $frete = 15.00;
        }

        $_SESSION['carrinho_totais'] = [
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'frete' => $frete,
            'total' => ($subtotal - $desconto) + $frete
        ];
    }
}
