<?php

header('Acces-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'Router.php';
require_once './config/Database.php';

require_once './models/Quote.php';

require_once './api/quote/QuoteController.php';

$database = new Database();
define("DB_CONN", $database->connect());

$router = new Router();
$root_path = getenv('ROOT_PATH');

$router->add("{$root_path}/api/quotes/{id}", 'GET', function($params) {
    $model = new Quote(DB_CONN);
    $quote_cont = new QuoteController($model);
    $quote_return = null;
    $quote_id = $params['id'];

    if ($quote_id) {
        $quote_return = $quote_cont->read_one($quote_id);
    } else {
        $quote_return = $quote_cont->read_all();
    }

    echo $quote_return;
});

$router->add('/users/{id}', 'GET', function($params) {
    echo "User ID: " . $params['id'];
});

$router->run();
