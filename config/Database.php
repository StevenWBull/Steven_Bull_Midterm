<?php
final class Database {
    private $conn = null;

    public function __construct() {
        try {
            $pgsql_conn_str = "pgsql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_NAME') . ";sslmode=require;sslcert=~/.postgresql/postgresql.crt";
            $this->conn = new PDO($pgsql_conn_str, getenv('DB_USERNAME'), getenv('DB_PASS'));
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