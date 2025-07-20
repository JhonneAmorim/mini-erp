<?php

class Router
{
    protected $routes = [];

    public function add($method, $uri, $action)
    {
        $this->routes[$method][$uri] = $action;
    }

    public function dispatch($uri)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (array_key_exists($uri, $this->routes[$method])) {
            list($controller, $method) = explode('@', $this->routes[$method][$uri]);
            $controllerInstance = new $controller();
            $controllerInstance->$method();
        } else {
            http_response_code(404);
            echo "<h1>404 - Página não encontrada</h1>";
        }
    }
}
