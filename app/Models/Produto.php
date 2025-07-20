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
        $sql = "SELECT p.*, e.variacao, e.quantidade FROM produtos p
                LEFT JOIN estoque e ON p.id = e.produto_id
                ORDER BY p.nome";

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

    public function save($produtoData, $variacoes = [])
    {
        $nome = $this->db->real_escape_string($produtoData['nome']);
        $preco = (float)$produtoData['preco'];
        $data_criacao = date('Y-m-d H:i:s');

        $sql = "INSERT INTO produtos (nome, preco, data_criacao) 
            VALUES ('$nome', $preco, '$data_criacao')";

        if ($this->db->query($sql)) {
            $produto_id = $this->db->insert_id;

            foreach ($variacoes as $v) {
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
}
