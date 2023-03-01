<?php

require_once 'Router.php';

$router = new Router();
$root_path = getenv('ROOT_PATH');

$router->add("{$root_path}/api/", 'GET', function() {
    echo "Hello, world!";
});

$router->add('/users/{id}', 'GET', function($params) {
    echo "User ID: " . $params['id'];
});

$router->run();
