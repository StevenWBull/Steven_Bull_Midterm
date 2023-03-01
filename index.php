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

$router->add("{$root_path}/api/quotes/", 'GET', function($params) {
    $model = new Quote(DB_CONN);
    $quote_cont = new QuoteController($model);
    $quote_return = null;

    $quote_id = $params['id'];
    $author_id = $params['authorId'];
    $category_id = $params['categoryId'];

    if ($category_id && $author_id) {
        $quote_return = $quote_cont->read_all_from_author_with_category($author_id, $category_id);
    } else if ($author_id) {
        $quote_return = $quote_cont->read_all_from_author($author_id);
    } else if ($quote_id) {
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
