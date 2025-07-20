# Mini ERP - Sistema de Gestão Simplificado

## 📖 Sobre o Projeto

O Mini ERP é um sistema web leve desenvolvido em PHP puro, seguindo o padrão de arquitetura MVC. Ele foi projetado para gerenciar funcionalidades básicas de um e-commerce, como cadastro de produtos, controle de estoque, gerenciamento de pedidos, aplicação de cupons de desconto e notificações por e-mail.

Este projeto foi construído para demonstrar a implementação de funcionalidades essenciais de um sistema de gestão de forma prática, com código limpo e de fácil manutenção.

---

## ✨ Funcionalidades

O sistema conta com as seguintes funcionalidades implementadas:

- **Gestão de Produtos:**
  - Criação e atualização de produtos.
  - Suporte para múltiplas variações por produto (ex: cor, tamanho).
  - Controle de estoque individual para cada variação.
- **Carrinho de Compras:**
  - Adição de produtos ao carrinho de compras gerenciado por sessão.
  - Cálculo de frete dinâmico com base no subtotal do pedido.
- **Gestão de Pedidos:**
  - Checkout simplificado com formulário para dados do cliente.
  - Integração com a API **ViaCEP** para preenchimento automático de endereço.
  - Listagem de todos os pedidos realizados.
- **Sistema de Cupons:**
  - Criação de cupons de desconto (valor fixo ou percentual).
  - Validação por data de validade e valor mínimo do pedido.
  - Aplicação dinâmica do cupom no carrinho de compras.
- **Notificações por E-mail:**
  - Envio de e-mail de confirmação ao cliente após a finalização do pedido.
- **Webhook para Status de Pedidos:**
  - Endpoint para receber atualizações de status de sistemas externos (ex: gateway de pagamento).
  - Lógica para cancelar pedidos e devolver os itens ao estoque automaticamente.

---

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8+
- **Banco de Dados:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5
- **Dependências (via Composer):**
  - `phpmailer/phpmailer`: Para o envio de e-mails.

---

## 🚀 Instalação e Configuração

Siga os passos abaixo para executar o projeto em seu ambiente local.

### Pré-requisitos

- PHP (versão 8.0 ou superior)
- MySQL (ou um servidor de banco de dados compatível, como o MariaDB)
- Composer para gerenciamento de dependências.

### Passo a Passo

1.  **Clone o Repositório:**

    ```bash
    git clone [https://github.com/seu-usuario/mini-erp.git](https://github.com/seu-usuario/mini-erp.git)
    cd mini-erp
    ```

2.  **Instale as Dependências:**
    Execute o Composer para instalar o PHPMailer.

    ```bash
    composer install
    ```

3.  **Configure o Banco de Dados:**

    - Crie um novo banco de dados no seu servidor MySQL.
    - Importe o arquivo `config/mini_erp_db.sql` para criar todas as tabelas e a estrutura necessária.
    - Abra o arquivo `config/database.php` e atualize as credenciais de acesso ao seu banco de dados:
      ```php
      define('DB_HOST', 'localhost');
      define('DB_USER', 'seu_usuario_mysql');
      define('DB_PASS', 'sua_senha_mysql');
      define('DB_NAME', 'nome_do_seu_banco');
      ```

4.  **Configure o Serviço de E-mail:**
    Abra o arquivo `app/Services/EmailService.php` e insira as suas credenciais de servidor SMTP (recomenda-se usar um serviço como o Mailtrap para testes ou as credenciais do seu provedor de e-mail).

    ```php
    // app/Services/EmailService.php
    $this->mail->Host       = 'seu_servidor_smtp';
    $this->mail->Username   = 'seu_email@example.com';
    $this->mail->Password   = 'sua_senha_de_app';
    ```

5.  **Inicie o Servidor Local:**
    Use o servidor embutido do PHP para iniciar a aplicação. Aponte-o para a pasta `public`.

    ```bash
    php -S localhost:8000 -t public
    ```

6.  **Acesse a Aplicação:**
    Abra o seu navegador e acesse `http://localhost:8000`.

---

## 🔗 Webhook

O sistema possui um endpoint para receber atualizações de status de pedidos.

- **URL:** `http://localhost:8000/webhook/pedidos`
- **Método:** `POST`
- **Corpo da Requisição (Body):** JSON

#### Exemplo: Atualizar o Status de um Pedido

```json
{
  "pedido_id": 1,
  "status": "pagamento_aprovado"
}
```

#### Exemplo: Cancelar um Pedido (e devolver ao estoque)

```json
{
  "pedido_id": 2,
  "status": "cancelado"
}
```

Você pode usar ferramentas como o **Insomnia** ou **Postman** para testar este endpoint.

---

## 👨‍💻 Autor

**Jhonne Amorim**

- [GitHub](https://github.com/jhonneamorim)
- [LinkedIn](https://www.linkedin.com/in/jhonne-amorim-oliveira-b8b95a243/)
