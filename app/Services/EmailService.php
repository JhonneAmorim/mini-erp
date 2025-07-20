<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host       = 'sandbox.smtp.mailtrap.io';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = '1e85acc1d6c430';
        $this->mail->Password   = '73692d4ccece63';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        $this->mail->CharSet    = 'UTF-8';
    }

    public function sendOrderConfirmation($pedidoData, $itensCarrinho)
    {
        try {
            $this->mail->setFrom('jhonneamorimao@gmail.com', 'Sua Loja Mini ERP');
            $this->mail->addAddress($pedidoData['cliente_email'], $pedidoData['cliente_nome']);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Confirmação do seu Pedido #' . $pedidoData['pedido_id'];
            $this->mail->Body    = $this->generateEmailBody($pedidoData, $itensCarrinho);
            $this->mail->AltBody = 'Seu pedido foi confirmado! Para ver os detalhes, por favor, use um cliente de e-mail que suporte HTML.';

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    private function generateEmailBody($pedidoData, $itensCarrinho)
    {
        ob_start();

        require __DIR__ . '/../Views/emails/order_confirmation.php';

        return ob_get_clean();
    }
}
