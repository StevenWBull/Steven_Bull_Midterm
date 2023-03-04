<?php

class CategoryController {
    private $model = null;

    public function __construct($model) {
        $this->model = $model;
    }

    private function no_data_found() {
        return json_encode(
            array('message' => 'category_id Not Found')
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
        $category_arr = array();
        $category_arr['data_count'] = $count;
        $category_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            // unpack $row into corresponding variables
            extract($row);

            $category_item = array(
                'id' => $id,
                'category' => $category
            );

            array_push($category_arr['data'], $category_item);
        }

        if ($random) {
            $randIdx = array_rand($category_arr['data']);
            $category_arr['data'] = array($category_arr['data'][$randIdx]);
            $category_arr['data_count'] = count($category_arr['data']);
        }

        if (count($category_arr['data']) > 1) 
            return json_encode($category_arr['data']);
        else
            return json_encode($category_arr['data'][0]);
    }

    public function read_all($random) {
        try {
            $category_model = $this->model;
    
            $result = $category_model->read_all();
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else 
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function read_one($category_id, $random) {
        try {
            $category_model = $this->model;
    
            $result = $category_model->read_one($category_id);
    
            $num = $result->rowCount();
    
            if ($num > 0)
                return $this->create_return_arr($result, $num, $random);
            else
                return $this->no_data_found();
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function create($category) {
        try {
            $category_model = $this->model;
    
            $result = $category_model->create($category);
        
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

    public function update($category_id, $category) {
        try {
            $category_model = $this->model;
    
            $result = $category_model->update($category_id, $category);
            $num = $result->rowCount();

            if ($num) {
                return $this->create_return_arr($result, $num);
            } else {
                return json_encode(array(
                    'message' => "category_id Not Found"
                ));
            }
        } catch (Throwable $e) {
            return $this->fatal_error(__FUNCTION__, $e->getMessage());
        }
    }

    public function delete($category_id) {
        try {
            $category_model = $this->model;
    
            $result = $category_model->delete($category_id);

            if ($result instanceof PDOStatement) {
                $num = $result->rowCount();

                if ($num)
                    return $this->create_return_arr($result, $num);
                else {
                    return json_encode(array(
                        'message' => "category_id Not Found"
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