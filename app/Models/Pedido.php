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
                "INSERT INTO pedidos (cliente_nome, cliente_email, cep, endereco, subtotal, valor_frete, cupom_id, valor_desconto, valor_total, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendente')"
            );
            $stmt->bind_param(
                "ssssddids",
                $pedidoData['cliente_nome'],
                $pedidoData['cliente_email'],
                $pedidoData['cep'],
                $pedidoData['endereco'],
                $pedidoData['subtotal'],
                $pedidoData['frete'],
                $pedidoData['cupom_id'],
                $pedidoData['desconto'],
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
            return $pedido_id;
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

    public function cancelOrderAndRestock($pedidoId)
    {
        $pedidoId = (int)$pedidoId;

        $this->db->begin_transaction();

        try {
            $sqlItens = "SELECT produto_id, variacao, quantidade FROM pedido_itens WHERE pedido_id = ?";
            $stmtItens = $this->db->prepare($sqlItens);
            $stmtItens->bind_param("i", $pedidoId);
            $stmtItens->execute();
            $itensResult = $stmtItens->get_result();
            $itens = $itensResult->fetch_all(MYSQLI_ASSOC);
            $stmtItens->close();

            if (!empty($itens)) {
                $stmtEstoque = $this->db->prepare("UPDATE estoque SET quantidade = quantidade + ? WHERE produto_id = ? AND variacao <=> ?");

                foreach ($itens as $item) {
                    $stmtEstoque->bind_param("iis", $item['quantidade'], $item['produto_id'], $item['variacao']);
                    $stmtEstoque->execute();
                }
                $stmtEstoque->close();
            }

            $stmtDelete = $this->db->prepare("DELETE FROM pedidos WHERE id = ?");
            $stmtDelete->bind_param("i", $pedidoId);
            $stmtDelete->execute();
            $stmtDelete->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erro ao cancelar pedido: " . $e->getMessage());
            return false;
        }
    }
}
