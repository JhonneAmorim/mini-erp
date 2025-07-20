CREATE DATABASE mini_erp_db;

USE mini_erp_db;

CREATE TABLE `produtos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(255) NOT NULL,
  `preco` DECIMAL(10, 2) NOT NULL,
  `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `estoque` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `produto_id` INT NOT NULL,
  `variacao` VARCHAR(100) DEFAULT NULL,
  `quantidade` INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(50) UNIQUE NOT NULL,
  `tipo_desconto` ENUM('percentual', 'fixo') NOT NULL,
  `valor` DECIMAL(10, 2) NOT NULL,
  `valor_minimo_pedido` DECIMAL(10, 2) DEFAULT 0.00,
  `data_validade` DATE NOT NULL,
  `ativo` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedidos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cliente_nome` VARCHAR(255) NOT NULL,
  `cliente_email` VARCHAR(255) NOT NULL,
  `cep` VARCHAR(9) NOT NULL,
  `endereco` VARCHAR(255) NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `valor_frete` DECIMAL(10, 2) NOT NULL,
  `cupom_id` INT NULL,
  `valor_desconto` DECIMAL(10, 2) DEFAULT 0.00,
  `valor_total` DECIMAL(10, 2) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pendente',
  `data_pedido` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cupom_id`) REFERENCES `cupons`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedido_itens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `variacao` VARCHAR(100) DEFAULT NULL,
  `quantidade` INT NOT NULL,
  `preco_unitario` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;