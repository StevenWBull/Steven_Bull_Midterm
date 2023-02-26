<?php
    // Load in env
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env');
    $dotenv->load();

    class Database {
        private $host = $_SERVER['APP_ENV'] === 'prod' ? $_ENV['DB_HOST'] : $_ENV['DB_DEV_HOST'];
        private $db_name = $_SERVER['APP_ENV'] === 'prod' ? $_ENV['DB_NAME'] : $_ENV['DB_DEV_NAME'];
        private $username = $_SERVER['APP_ENV'] === 'prod' ? $_ENV['DB_USERNAME'] : $_ENV['DB_DEV_USERNAME'];
        private $password = $_SERVER['APP_ENV'] === 'prod' ? $_ENV['DB_PASS'] : $_ENV['DB_DEV_PASS'];
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