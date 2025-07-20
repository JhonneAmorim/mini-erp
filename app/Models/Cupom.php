<?php

class Cupom
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM cupons ORDER BY data_validade DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($dados)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO cupons (codigo, tipo_desconto, valor, valor_minimo_pedido, data_validade, ativo) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssddsi",
            $dados['codigo'],
            $dados['tipo_desconto'],
            $dados['valor'],
            $dados['valor_minimo_pedido'],
            $dados['data_validade'],
            $dados['ativo']
        );
        return $stmt->execute();
    }

    public function findByCode($codigo)
    {
        $stmt = $this->db->prepare("SELECT * FROM cupons WHERE codigo = ? AND ativo = 1 AND data_validade >= CURDATE()");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
