<?php

class Router {
    protected $routes = [];
    protected $params = [];
    protected $post_data = [];

    private function sanitize($data) {
        // Remove white space from beginning and end of the string
        $data = trim($data);
        // Strip tags to remove any HTML or PHP tags
        $data = strip_tags($data);
        // Convert special characters to HTML entities to prevent XSS attacks
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $data;
    }

    public function add($route, $method = 'GET', $function) {
        $this->routes[$route] = [
            'method' => $method,
            'function' => $function
        ];
    }

    public function run() {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'OPTIONS') {
            header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
            exit();
        }
    
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
                    } else if (preg_match('/^({.*})$/', $route_parts[$i])) {
                        $params[substr($route_parts[$i], 1, -1)] = $this->sanitize($uri_parts[$i]);
                    }
    
                    if ($i == count($uri_parts) - 1) {
                        if ($method === 'GET') {
                            if (!empty($_GET)) {
                                foreach ($_GET as $key => $value) {
                                    // sanitize all uri params
                                    $sanitized_value = $this->sanitize($value);
                                    $query_params[$key] = $sanitized_value;
                                }
                            }
                            $this->params = array_merge($params, $query_params);
                            call_user_func_array($data['function'], [$this->params]);
                        } else {
                            $this->post_data = json_decode(file_get_contents('php://input'), true);
                            foreach ($this->post_data as $key => $value) {
                                $this->post_data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
                            }
                            call_user_func_array($data['function'], [$this->post_data]);
                        }
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