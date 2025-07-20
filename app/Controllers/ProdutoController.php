<?php

class ProdutoController
{
    public function index()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->getAll();

        require_once '../app/Views/produtos/index.php';
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(404);
            echo "<h1>404 - Produto não encontrado</h1>";
            return;
        }

        $produtoModel = new Produto();
        $produto = $produtoModel->findWithVariations($id);

        if (!$produto) {
            http_response_code(404);
            echo "<h1>404 - Produto não encontrado</h1>";
            return;
        }

        echo json_encode($produto);
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoModel = new Produto();
            $produtoData = [
                'id' => $_POST['id'] ?? null,
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
