<?php
    // Headers
    header('Acces-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    
    try {
        include_once '../../config/Database.php';
        include_once '../../models/blueprint.php';

        $database = new Database();
        $db = $databse->connect();

        $blueprint = new Blueprint($db);

        $result = $blueprint->read();

        $num = $result->rowCount();

        if ($num > 0) {
            $blueprint_arr = array();
            $blueprint_arr['data'] = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                // unpack $row into corresponding variables
                extract($row);

                $blueprint_item = array(
                    'id' => $id,
                    'created_at' => $created_at //etc
                )

                array_push($blueprint_arr['data'], $blueprint_item);

                echo json_encode($blueprint_arr);
            }
        } else {
            echo json_encode(
                array('message' => 'No Data Found.')
            );
        }
    } catch (Error $e) {
        echo 'Error in Blueprint read';
    }