<?php
    // Headers
    header('Acces-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    try {
        include_once '../../config/Database.php';
        include_once '../../models/Quote.php';

        $database = new Database();
        $db = $database->connect();

        $quote = new Quote($db);

        $result = $quote->read_all();

        $num = $result->rowCount();

        if ($num > 0) {
            $quote_arr = array();
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

            echo json_encode($quote_arr);
        } else {
            echo json_encode(
                array('message' => 'No Data Found.')
            );
        }
    } catch (Throwable $e) {
        echo 'Error in quote read! ' . $e->getMessage();
    }