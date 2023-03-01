<?php

class AuthorController {
    private $model = null;

    public function __construct($model) {
        $this->model = $model;
    }

    private function no_data_found() {
        header('HTTP/1.1 404 Not Found');
        return json_encode(
            array('message' => 'author_id Not Found.')
        ); 
    }

    private function fatal_error($fn, $msg) {
        $class_name = debug_backtrace()[1]['class'];
        if (getenv('APP_ENV') === 'prod')
            return; // Would send to the used logger here like Datadog
        else
            return "Error in {$class_name}->{$fn}: {$msg}";
    }

    private function create_return_arr($result, $count, $random) {
        $author_arr = array();
        $author_arr['data_count'] = $count;
        $author_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            // unpack $row into corresponding variables
            extract($row);

            $quote_item = array(
                'id' => $id,
                'author' => $author
            );

            array_push($author_arr['data'], $quote_item);
        }

        if ($random) {
            $randIdx = array_rand($author_arr['data']);
            $author_arr['data'] = array($author_arr['data'][$randIdx]);
            $author_arr['data_count'] = count($author_arr['data']);
        }

        return json_encode($author_arr);
    }

    public function read_all($random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_all();
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else 
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function read_one($author_id, $random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_one($author_id);
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }
}