<?php
$pageTitle = $pageTitle ?? 'Mini ERP';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">
                    <i class="bi bi-box-seam"></i> Mini ERP
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="/produtos"><i class="bi bi-tags-fill"></i> Produtos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pedidos"><i class="bi bi-receipt"></i> Pedidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/cupons"><i class="bi bi-ticket-percent-fill"></i> Cupons</a>
                        </li>
                    </ul>
                    <a href="/carrinho" class="btn btn-outline-light position-relative">
                        <i class="bi bi-cart"></i> Carrinho
                        <span id="cart-count"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0; ?>
                            <span class="visually-hidden">itens no carrinho</span>
                        </span>
                    </a>
                </div>
            </div>
        </nav>

        <main class="container flex-grow-1">