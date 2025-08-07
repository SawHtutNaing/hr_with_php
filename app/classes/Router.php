<?php

class Router {
    private $routes = [];

    public function add($uri, $file) {
        $this->routes[$uri] = $file;
    }

    public function dispatch($uri) {
        if (array_key_exists($uri, $this->routes)) {
            require ROOT_PATH . $this->routes[$uri];
        } else {
            http_response_code(404);
            require ROOT_PATH . 'app/views/404.php';
        }
    }
}
