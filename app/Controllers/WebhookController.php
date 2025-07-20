<?php

class WebhookController
{
    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
            return;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['pedido_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos ou ausentes.']);
            return;
        }

        $pedidoId = (int)$data['pedido_id'];
        $status = strtolower(trim($data['status']));

        $pedidoModel = new Pedido();

        if ($status === 'cancelado') {
            if ($pedidoModel->cancelOrderAndRestock($pedidoId)) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Pedido cancelado e estoque atualizado.']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Erro ao cancelar o pedido.']);
            }
        } else {
            if ($pedidoModel->updateStatus($pedidoId, $status)) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Status do pedido atualizado.']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o status do pedido.']);
            }
        }
    }
}
