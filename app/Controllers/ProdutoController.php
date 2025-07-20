<?php

class ProdutoController
{
    public function index()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->getAll();

        require_once '../app/Views/produtos/index.php';
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoModel = new Produto();
            $produtoData = [
                'nome' => $_POST['nome'] ?? '',
                'preco' => $_POST['preco'] ?? 0
            ];

            $variacoes = $_POST['variacoes'] ?? [];

            if ($produtoModel->save($produtoData, $variacoes)) {
                header('Location: /produtos');
                exit;
            } else {
                echo "Erro ao salvar o produto.";
            }
        } else {
            echo "Método não permitido.";
        }
    }
}
