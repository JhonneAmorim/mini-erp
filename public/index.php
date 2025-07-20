<?php

session_start();

require_once '../config/database.php';
require_once '../app/Core/Router.php';

spl_autoload_register(function ($className) {
    if (file_exists('../app/Controllers/' . $className . '.php')) {
        require_once '../app/Controllers/' . $className . '.php';
    } elseif (file_exists('../app/Models/' . $className . '.php')) {
        require_once '../app/Models/' . $className . '.php';
    }
});

$router = new Router();

// Rotas de produtos
$router->add('GET', 'produtos', 'ProdutoController@index');
$router->add('GET', 'produtos/editar', 'ProdutoController@editar');
$router->add('POST', 'produtos/salvar', 'ProdutoController@salvar');

$url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$router->dispatch($url);
