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

            try {
                $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PSO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo 'Connectection Error: ' . $e->getMessage();
            }

            return $this->conn;
        }
    }