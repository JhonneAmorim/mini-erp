<?php

class Pedido
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function delete($id)
    {
        $id = (int)$id;
        $sql = "DELETE FROM pedidos WHERE id = $id";
        return $this->db->query($sql);
    }

    public function updateStatus($id, $status)
    {
        $id = (int)$id;
        $status = $this->db->real_escape_string($status);
        $sql = "UPDATE pedidos SET status = '$status' WHERE id = $id";
        return $this->db->query($sql);
    }

    public function updateEstoque($produtoId, $quantidade)
    {
        $produtoId = (int)$produtoId;
        $quantidade = (int)$quantidade;
        $sql = "UPDATE estoque SET quantidade = quantidade + $quantidade WHERE produto_id = $produtoId";
        return $this->db->query($sql);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM pedidos ORDER BY data_criacao DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = (int)$id;
        $sql = "SELECT * FROM pedidos WHERE id = $id";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }
}
