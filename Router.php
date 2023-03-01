<?php

class Router {
    protected $routes = [];
    protected $params = [];

    public function add($route, $method = 'GET', $function) {
        $this->routes[$route] = [
            'method' => $method,
            'function' => $function
        ];
    }

    public function run() {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $method = $_SERVER['REQUEST_METHOD'];
    
        $route_found = false;
    
        foreach ($this->routes as $route => $data) {
            $route_parts = explode('/', trim($route, '/'));
            $uri_parts = explode('/', $uri);
    
            if (count($route_parts) == count($uri_parts) && $data['method'] == $method) {
                $params = [];
                $query_params = [];
    
                for ($i = 0; $i < count($uri_parts); $i++) {
                    if ($route_parts[$i] != $uri_parts[$i] && !preg_match('/^({.*})$/', $route_parts[$i])) {
                        break;
                    } elseif (preg_match('/^({.*})$/', $route_parts[$i])) {
                        $params[substr($route_parts[$i], 1, -1)] = $uri_parts[$i];
                    }
    
                    if ($i == count($uri_parts) - 1) {
                        if (!empty($_GET)) {
                            $query_params = $_GET;
                        }
                        $this->params = array_merge($params, $query_params);
                        call_user_func_array($data['function'], [$this->params]);
                        $route_found = true;
                    }
                }
            }
        }
    
        if (!$route_found) {
            header('HTTP/1.1 404 Not Found');
            echo "404 Page not found";
        }
    }    
}