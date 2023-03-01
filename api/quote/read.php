<?php

function read_all($db) {
    try {
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

            return json_encode($quote_arr);
        } else {
            return json_encode(
                array('message' => 'No Data Found.')
            );
        }
    } catch (Throwable $e) {
        echo 'Error in quote read all! ' . $e->getMessage();
    }
}