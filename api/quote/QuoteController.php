<?php

include_once '../../models/Quote.php';

class QuoteController {
    private $model = null;

    public function __construct($model) {
        $this->model = $model;
    }

    private function no_data_found() {
        return json_encode(
            array('message' => 'No Quotes Found')
        ); 
    }

    private function fatal_error($fn, $msg) {
        $class_name = debug_backtrace()[1]['class'];
        if (getenv('APP_ENV') === 'prod')
            return; // Would send to the used logger here like Datadog
        else
            return "Error in {$class_name}->{$fn}: {$msg}";
    }

    private function create_return_arr($result, $count, $random = 0) {
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

        return json_encode($quote_arr['data']);
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

    public function read_one($id, $random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_one($id);
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function read_all_from_author($author_id, $random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_all_from_author($author_id);
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function read_all_from_author_with_category($author_id, $category_id, $random) {
        try {
            $quote = $this->model;
    
            $result = $quote->read_all_from_author_with_category($author_id, $category_id);
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function create($quote, $author_id, $category_id) {
        try {
            $model = $this->model;
    
            $result = $model->create($quote, $author_id, $category_id);
        
            if ($result instanceof PDOStatement) {
                header('HTTP/1.1 201 Created');
                $num = $result->rowCount();
                return $this->create_return_arr($result, $num);
            } else {
                return json_encode($result);
            }
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function update($quote_id, $quote, $author_id, $category_id) {
        try {
            $category_model = $this->model;
    
            $result = $category_model->update($quote_id, $quote, $author_id, $category_id);
            

            if ($result instanceof PDOStatement && $result->rowCount()) {
                $num = $result->rowCount();
                return $this->create_return_arr($result, $num);
            } else {
                return json_encode(array(
                    'message' => "No Quotes Found"
                ));
            }
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function delete($quote_id) {
        try {
            $category_model = $this->model;
    
            $result = $category_model->delete($quote_id);

            if ($result instanceof PDOStatement) {
                $num = $result->rowCount();

                if ($num)
                    return $this->create_return_arr($result, $num);
                else {
                    return json_encode(array(
                        'message' => "quote_id Not Found"
                    ));
                }
            } else {
                return json_encode($result);
            }
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }
}