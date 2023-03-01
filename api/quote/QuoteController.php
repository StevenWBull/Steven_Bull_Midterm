<?php

include_once '../../config/Database.php';
include_once '../../models/Quote.php';

class QuoteController() {
    private $db = null;

    public function __construct() {
        $database = new Database();
        $this->$db = $database.connect();
    }
}