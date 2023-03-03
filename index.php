<?php

header('Acces-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'Router.php';
require_once './config/Database.php';

require_once './models/Quote.php';
require_once './models/Author.php';
require_once './models/Category.php';

require_once './api/quote/QuoteController.php';
require_once './api/author/AuthorController.php';
require_once './api/category/CategoryController.php';

define("DATABASE", new Database());
define("DB_CONN", DATABASE->connect());
define("ROUTER", new Router());
define("ROOT_PATH", getenv('ROOT_PATH'));

ROUTER->add("{ROOT_PATH}/api/quotes/", 'GET', function($params) {
    $model = new Quote(DB_CONN);
    $controller = new QuoteController($model);
    $return_stmt = null;

    $quote_id = $params['id'];
    $author_id = $params['authorId'];
    $category_id = $params['categoryId'];
    $random = $params['random'] === 'true' ? true : false;

    if ($category_id && $author_id)
        $return_stmt = $controller->read_all_from_author_with_category($author_id, $category_id, $random);
    else if ($author_id)
        $return_stmt = $controller->read_all_from_author($author_id, $random);
    else if ($quote_id)
        $return_stmt = $controller->read_one($quote_id, $random);
    else
        $return_stmt = $controller->read_all($random);

    echo $return_stmt;
});

ROUTER->add('{ROOT_PATH}/api/authors', 'GET', function($params) {
    $model = new Author(DB_CONN);
    $controller = new AuthorController($model);
    $return_stmt = null;

    $author_id = $params['id'];
    $random = $params['random'] === 'true' ? true : false;

    if ($author_id)
        $return_stmt = $controller->read_one($author_id, $random);
    else
        $return_stmt = $controller->read_all($random);

    echo $return_stmt;
});

ROUTER->add('{ROOT_PATH}/api/categories', 'GET', function($params) {
    $model = new Category(DB_CONN);
    $controller = new CategoryController($model);
    $return_stmt = null;

    $author_id = $params['id'];
    $random = $params['random'] === 'true' ? true : false;

    if ($author_id)
        $return_stmt = $controller->read_one($author_id, $random);
    else
        $return_stmt = $controller->read_all($random);

    echo $return_stmt;
});

ROUTER->add("{ROOT_PATH}/api/authors/", 'POST', function($post_data) {
    $model = new Author(DB_CONN);
    $controller = new AuthorController($model);
    $return_stmt = null;

    $author = $post_data['author'];

    if (!$author) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(
            array('message' => "'author' Field Required.")
        );
        return;
    }

    $return_stmt = $controller->create($author);

    echo $return_stmt;
});

ROUTER->add("{ROOT_PATH}/api/categories/", 'POST', function($post_data) {
    $model = new Category(DB_CONN);
    $controller = new CategoryController($model);
    $return_stmt = null;

    $category = $post_data['category'];

    if (!$category) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(
            array('message' => "'category' Field Required.")
        );
        return;
    }

    $return_stmt = $controller->create($category);

    echo $return_stmt;
});

ROUTER->add("{ROOT_PATH}/api/quotes/", 'POST', function($post_data) {
    $model = new Quote(DB_CONN);
    $controller = new QuoteController($model);
    $return_stmt = null;

    $quote = $post_data['quote'];
    $author_id = $post_data['author_id'];
    $category_id = $post_data['category_id'];

    if (!$quote || !$category_id || !$author_id) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->create($quote, $author_id, $category_id);

    echo $return_stmt;
});

ROUTER->run();