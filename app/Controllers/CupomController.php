<?php

class CupomController
{
    public function index()
    {
        $cupomModel = new Cupom();
        $cupons = $cupomModel->getAll();
        require_once '../app/Views/cupons/index.php';
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'codigo' => $_POST['codigo'],
                'tipo_desconto' => $_POST['tipo_desconto'],
                'valor' => (float)$_POST['valor'],
                'valor_minimo_pedido' => (float)($_POST['valor_minimo_pedido'] ?? 0),
                'data_validade' => $_POST['data_validade'],
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
            ];

            $cupomModel = new Cupom();
            if ($cupomModel->create($dados)) {
                header('Location: /cupons');
                exit;
            } else {
                echo "Erro ao salvar o cupom.";
            }
        }
    }
}
