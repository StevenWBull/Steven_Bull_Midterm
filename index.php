<?php

header('Acces-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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
define("THE_ROOT", getenv('THE_ROOT') ? getenv('THE_ROOT') : '');

// GET REQUESTS
ROUTER->add("/", 'GET', function($params) {
    echo "Hello, world!";
});

ROUTER->add(THE_ROOT . "/api/quotes", 'GET', function($params) {
    $model = new Quote(DB_CONN);
    $controller = new QuoteController($model);
    $return_stmt = null;

    $quote_id = $params['id'];
    $author_id = $params['author_id'];
    $category_id = $params['category_id'];
    $random = $params['random'] === 'true' ? true : false;

    if ($category_id && $author_id)
        $return_stmt = $controller->read_all_from_author_with_category($author_id, $category_id, $random);
    else if ($author_id)
        $return_stmt = $controller->read_all_from_author($author_id, $random);
    else if ($category_id)
        $return_stmt = $controller->read_all_from_category($category_id, $random);
    else if ($quote_id)
        $return_stmt = $controller->read_one($quote_id, $random);
    else
        $return_stmt = $controller->read_all($random);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/authors", 'GET', function($params) {
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

ROUTER->add(THE_ROOT . "/api/categories", 'GET', function($params) {
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

// POST REQUESTS
ROUTER->add(THE_ROOT . "/api/quotes", 'POST', function($post_data) {
    $model = new Quote(DB_CONN);
    $controller = new QuoteController($model);
    $return_stmt = null;

    $quote = $post_data['quote'];
    $author_id = $post_data['author_id'];
    $category_id = $post_data['category_id'];

    if (!$quote || !$category_id || !$author_id) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->create($quote, $author_id, $category_id);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/authors", 'POST', function($post_data) {
    $model = new Author(DB_CONN);
    $controller = new AuthorController($model);
    $return_stmt = null;

    $author = $post_data['author'];

    if (!$author) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->create($author);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/categories", 'POST', function($post_data) {
    $model = new Category(DB_CONN);
    $controller = new CategoryController($model);
    $return_stmt = null;

    $category = $post_data['category'];

    if (!$category) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->create($category);

    echo $return_stmt;
});

// PUT REQUESTS
ROUTER->add(THE_ROOT . "/api/quotes", 'PUT', function($post_data) {
    $model = new Quote(DB_CONN);
    $controller = new QuoteController($model);
    $return_stmt = null;

    $quote_id = $post_data['id'];
    $quote = $post_data['quote'];
    $author_id = $post_data['author_id'];
    $category_id = $post_data['category_id'];

    if (!$quote_id || !$quote || !$category_id || !$author_id) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->update($quote_id, $quote, $author_id, $category_id);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/authors", 'PUT', function($post_data) {
    $model = new Author(DB_CONN);
    $controller = new AuthorController($model);
    $return_stmt = null;

    $author_id = $post_data['id'];
    $author = $post_data['author'];

    if (!$author || !$author_id ) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->update($author_id, $author);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/categories", 'PUT', function($post_data) {
    $model = new Category(DB_CONN);
    $controller = new CategoryController($model);
    $return_stmt = null;

    $category_id = $post_data['id'];
    $category = $post_data['category'];

    if (!$category || !$category_id) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->update($category_id, $category);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/quotes", 'DELETE', function($post_data) {
    $model = new Quote(DB_CONN);
    $controller = new QuoteController($model);
    $return_stmt = null;

    $quote_id = $post_data['id'];

    if (!$quote_id ) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->delete($quote_id);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/authors", 'DELETE', function($post_data) {
    $model = new Author(DB_CONN);
    $controller = new AuthorController($model);
    $return_stmt = null;

    $author_id = $post_data['id'];

    if (!$author_id ) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->delete($author_id);

    echo $return_stmt;
});

ROUTER->add(THE_ROOT . "/api/categories", 'DELETE', function($post_data) {
    $model = new Category(DB_CONN);
    $controller = new CategoryController($model);
    $return_stmt = null;

    $category_id = $post_data['id'];

    if (!$category_id ) {
        echo json_encode(
            array('message' => "Missing Required Parameters")
        );
        return;
    }

    $return_stmt = $controller->delete($category_id);

    echo $return_stmt;
});

ROUTER->run();