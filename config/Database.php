<?php
    class Database {
        private $host = 'localhost';
        private $db_name = 'db name'
        private $username = 'root';
        private $password = '';
        private $conn;

        // DB Connect
        public function connect() {
            $this->conn = null;
        }
    }