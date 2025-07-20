<?php

class Pedido
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($pedidoData, $itensCarrinho)
    {
        $this->db->begin_transaction();

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO pedidos (cliente_nome, cliente_email, cep, endereco, subtotal, valor_frete, valor_total, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendente')"
            );
            $stmt->bind_param(
                "ssssddd",
                $pedidoData['cliente_nome'],
                $pedidoData['cliente_email'],
                $pedidoData['cep'],
                $pedidoData['endereco'],
                $pedidoData['subtotal'],
                $pedidoData['frete'],
                $pedidoData['total']
            );
            $stmt->execute();
            $pedido_id = $this->db->insert_id;
            $stmt->close();

            $stmtItem = $this->db->prepare(
                "INSERT INTO pedido_itens (pedido_id, produto_id, variacao, quantidade, preco_unitario) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmtEstoque = $this->db->prepare(
                "UPDATE estoque SET quantidade = quantidade - ? WHERE id = ?"
            );

            foreach ($itensCarrinho as $item) {
                $stmtItem->bind_param(
                    "iisid",
                    $pedido_id,
                    $item['produto_id'],
                    $item['variacao'],
                    $item['quantidade'],
                    $item['preco']
                );
                $stmtItem->execute();

                $stmtEstoque->bind_param(
                    "ii",
                    $item['quantidade'],
                    $item['estoque_id']
                );
                $stmtEstoque->execute();
            }
            $stmtItem->close();
            $stmtEstoque->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
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
        $sql = "SELECT * FROM pedidos ORDER BY data_pedido DESC";
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
