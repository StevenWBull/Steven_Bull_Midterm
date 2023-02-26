<?php
    // Headers
    header('Acces-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    try {
        include_once '../../config/Database.php';
        include_once '../../models/Band.php';

        $database = new Database();
        $db = $database->connect();

        $band = new Band($db);

        $result = $band->read();

        $num = $result->rowCount();

        if ($num > 0) {
            $band_arr = array();
            $band_arr['data'] = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                // unpack $row into corresponding variables
                extract($row);

                $band_item = array(
                    'id' => $id,
                    'created_at' => $created_at,
                    'name' => $name,
                    'founding_year' => $founding_year,
                    'genre_name' => $genre_name
                );

                array_push($band_arr['data'], $band_item);
            }

            echo json_encode($band_arr);
        } else {
            echo json_encode(
                array('message' => 'No Data Found.')
            );
        }
    } catch (Throwable $e) {
        echo 'Error in band read! ' . $e->getMessage();
    }