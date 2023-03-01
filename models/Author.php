<?php 
    class Author {
        // Connect to DB
        private $conn;
        private $table = 'authors';

        // Table columns
        public $id;
        public $author;

        private $select_stmt;

        // Constructor
        public function __construct($db) {
            $this->conn = $db;
            $this->select_stmt = "
                SELECT id, autho
                FROM {$this->table} a
            ";
        }

        private function fatal_error($msg) {
            header('HTTP/1.1 500 Internal Server Error');

            if (getenv('APP_ENV') === 'prod')
                echo json_encode(
                    array('message' => 'Internal Server Error')
                ); 
            else
                echo 'Caught exception: ' . $msg;
        }

        // Get
        public function read_all() {
            try {
                $query = $this->select_stmt . ";";

                // Prepare statement
                $stmt = $this->conn->prepare($query);

                // Execute Query
                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }

        public function read_one($author_id) {
            try {
                $query = $this->select_stmt . "WHERE id = {$author_id};";

                // Prepare statement
                $stmt = $this->conn->prepare($query);

                // Execute Query
                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }
    }