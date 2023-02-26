<?php
    class Database {
        private $conn = null;

        public function __construct() {
            try {
                $mysql_conn_str = "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME');
                $this->conn = new PDO($mysql_conn_str, getenv('DB_USERNAME'), getenv('DB_PASS'));
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo 'Connection Error: ' . $e->getMessage();
            }
        }

        // DB Connect
        public function connect() {
            return $this->conn;
        }
    }