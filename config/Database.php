<?php
final class Database {
    private $conn = null;

    public function __construct() {
        try {
            // Parse the connection string
            $parsed = parse_url(getenv('DATABASE_URL'));

            // Extract the different components
            $host = $parsed['host'];
            $port = $parsed['port'];
            $db_name = ltrim($parsed['path'], '/');
            $username = $parsed['user'];
            $password = $parsed['pass'];

            $pgsql_conn_str = "pgsql:host=" . $host . ";port=" . $port . ";dbname=" . $db_name . ";sslmode=require;sslcert=~/.postgresql/postgresql.crt";
            $this->conn = new PDO($pgsql_conn_str, $username, $password);
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