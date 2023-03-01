<?php

header('Acces-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'Router.php';
require_once './config/Database.php';

require_once './models/Quote.php';
require_once './models/Author.php';

require_once './api/quote/QuoteController.php';
require_once './api/author/AuthorController.php';

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
    $random = $params['random'] === 'true' ? true : false;

    if ($category_id && $author_id) {
        $quote_return = $quote_cont->read_all_from_author_with_category($author_id, $category_id, $random);
    } else if ($author_id) {
        $quote_return = $quote_cont->read_all_from_author($author_id, $random);
    } else if ($quote_id) {
        $quote_return = $quote_cont->read_one($quote_id, $random);
    } else {
        $quote_return = $quote_cont->read_all($random);
    }

    echo $quote_return;
});

$router->add('{$root_path}/api/authors', 'GET', function($params) {
    $model = new Author(DB_CONN);
    $author_cont = new AuthorController($model);
    $author_return = null;

    $author_id = $params['id'];
    $random = $params['random'] === 'true' ? true : false;

    if ($author_id) {
        $author_return = $author_cont->read_one($author_id, $random);
    } else {
        $author_return = $author_cont->read_all($random);
    }

    echo $author_return;
});

$router->run();