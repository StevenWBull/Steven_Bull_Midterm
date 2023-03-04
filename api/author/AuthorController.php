<?php

class AuthorController {
    private $model = null;

    public function __construct($model) {
        $this->model = $model;
    }

    private function no_data_found() {
        return json_encode(
            array('message' => 'author_id Not Found')
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
        $author_arr = array();
        $author_arr['data_count'] = $count;
        $author_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            // unpack $row into corresponding variables
            extract($row);

            if ($id && $author)
                $author_item = array(
                    'id' => $id,
                    'author' => $author
                );
            else
                $author_item = array(
                    'id' => $id
                );

            array_push($author_arr['data'], $author_item);
        }

        if ($random) {
            $randIdx = array_rand($author_arr['data']);
            $author_arr['data'] = array($author_arr['data'][$randIdx]);
            $author_arr['data_count'] = count($author_arr['data']);
        }

        if (count($author_arr['data']) > 1) 
            return json_encode($author_arr['data']);
        else
            return json_encode($author_arr['data'][0]);
    }

    public function read_all($random) {
        try {
            $author = $this->model;
    
            $result = $author->read_all();
    
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
            $author = $this->model;
    
            $result = $author->read_one($author_id);
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function create($author) {
        try {
            $author_model = $this->model;
    
            $result = $author_model->create($author);
        
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

    public function update($author_id, $author) {
        try {
            $author_model = $this->model;
    
            $result = $author_model->update($author_id, $author);
            $num = $result->rowCount();

            if ($num) {
                return $this->create_return_arr($result, $num);
            } else {
                return json_encode(array(
                    'message' => "author_id Not Found"
                ));
            }
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function delete($author_id) {
        try {
            $author_model = $this->model;
    
            $result = $author_model->delete($author_id);

            if ($result instanceof PDOStatement) {
                $num = $result->rowCount();

                if ($num)
                    return $this->create_return_arr($result, $num);
                else {
                    return json_encode(array(
                        'message' => "author_id Not Found"
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