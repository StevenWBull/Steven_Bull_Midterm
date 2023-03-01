<?php

include_once '../../models/Quote.php';

class QuoteController {
    private $model = null;

    public function __construct($model) {
        $this->model = $model;
    }

    private function create_return_arr($result, $count, $random) {
        $quote_arr = array();
        $quote_arr['data_count'] = $count;
        $quote_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            // unpack $row into corresponding variables
            extract($row);

            $quote_item = array(
                'id' => $id,
                'quote' => $quote,
                'author' => $author,
                'category' => $category
            );

            array_push($quote_arr['data'], $quote_item);
        }

        if ($random) {
            $randIdx = array_rand($quote_arr['data']);
            $quote_arr['data'] = array($quote_arr['data'][$randIdx]);
            $quote_arr['data_count'] = count($quote_arr['data']);
        }

        return json_encode($quote_arr);
    }

    public function read_all($random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_all();
    
            $num = $result->rowCount();
    
            if ($num > 0) {
                return $this->create_return_arr($result, $num, $random);
            } else {
                return json_encode(
                    array('message' => 'No Quotes Found.')
                );
            }
        } catch (Throwable $e) {
            return 'Error in QuoteController->read_all: ' . $e->getMessage();
        }
    }

    public function read_one($id, $random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_one($id);
    
            $num = $result->rowCount();
    
            if ($num > 0) {
                return $this->create_return_arr($result, $num, $random);
            } else {
                return json_encode(
                    array('message' => 'No Quotes Found.')
                );
            }
        } catch (Throwable $e) {
            return 'Error in QuoteController->read_one: ' . $e->getMessage();
        }
    }

    public function read_all_from_author($author_id, $random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_all_from_author($author_id);
    
            $num = $result->rowCount();
    
            if ($num > 0) {
                return $this->create_return_arr($result, $num, $random);
            } else {
                return json_encode(
                    array('message' => 'No Quotes Found.')
                );
            }
        } catch (Throwable $e) {
            return 'Error in QuoteController->read_all_from_author: ' . $e->getMessage();
        }
    }

    public function read_all_from_author_with_category($author_id, $category_id, $random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_all_from_author_with_category($author_id, $category_id);
    
            $num = $result->rowCount();
    
            if ($num > 0) {
                return $this->create_return_arr($result, $num, $random);
            } else {
                return json_encode(
                    array('message' => 'No Quotes Found.')
                );
            }
        } catch (Throwable $e) {
            return 'Error in QuoteController->read_all_from_author_with_category: ' . $e->getMessage();
        }
    }
}