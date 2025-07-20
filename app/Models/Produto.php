<?php

class Produto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $sql = "SELECT p.*, e.variacao, e.quantidade, e.id as estoque_id FROM produtos p
            LEFT JOIN estoque e ON p.id = e.produto_id
            ORDER BY p.id, p.nome";

        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = (int)$id;
        $sql = "SELECT * FROM produtos WHERE id = $id";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    public function findWithVariations($id)
    {
        $id = (int)$id;
        $produto = $this->find($id);

        if ($produto) {
            $sql = "SELECT * FROM estoque WHERE produto_id = $id";
            $result = $this->db->query($sql);
            $produto['variacoes'] = $result->fetch_all(MYSQLI_ASSOC);
        }

        return $produto;
    }

    public function save($produtoData, $variacoes = [])
    {
        if (isset($produtoData['id']) && is_numeric($produtoData['id']) && $produtoData['id'] > 0) {
            return $this->update($produtoData['id'], $produtoData, $variacoes);
        }

        $nome = $this->db->real_escape_string($produtoData['nome']);
        $preco = (float)$produtoData['preco'];
        $data_criacao = date('Y-m-d H:i:s');

        $sql = "INSERT INTO produtos (nome, preco, data_criacao) 
            VALUES ('$nome', $preco, '$data_criacao')";

        if ($this->db->query($sql)) {
            $produto_id = $this->db->insert_id;

            foreach ($variacoes as $v) {
                if (empty($v['nome']) && empty($v['estoque'])) continue;
                $variacaoNome = $this->db->real_escape_string($v['nome']);
                $quantidade = (int)$v['estoque'];

                $sqlEstoque = "INSERT INTO estoque (produto_id, variacao, quantidade) 
                           VALUES ($produto_id, '$variacaoNome', $quantidade)";
                $this->db->query($sqlEstoque);
            }

            return true;
        }

        return false;
    }

    public function update($id, $produtoData, $variacoes = [])
    {
        $id = (int)$id;
        $nome = $this->db->real_escape_string($produtoData['nome']);
        $preco = (float)$produtoData['preco'];

        $sql = "UPDATE produtos SET nome = '$nome', preco = $preco WHERE id = $id";

        if ($this->db->query($sql)) {
            // Deleta as variações antigas para inserir as novas.
            $this->db->query("DELETE FROM estoque WHERE produto_id = $id");

            foreach ($variacoes as $v) {
                if (empty($v['nome']) && empty($v['estoque'])) continue;
                $variacaoNome = $this->db->real_escape_string($v['nome']);
                $quantidade = (int)$v['estoque'];

                $sqlEstoque = "INSERT INTO estoque (produto_id, variacao, quantidade) 
                               VALUES ($id, '$variacaoNome', $quantidade)";
                $this->db->query($sqlEstoque);
            }
            return true;
        }

        return false;
    }

    public function findVariation($estoqueId)
    {
        $estoqueId = (int)$estoqueId;
        $sql = "SELECT p.id as produto_id, p.nome, p.preco, e.id as estoque_id, e.variacao, e.quantidade
                FROM estoque e
                JOIN produtos p ON e.produto_id = p.id
                WHERE e.id = $estoqueId";

        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }
}
