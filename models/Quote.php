<?php 
    class Quote {
        // Connect to DB
        private $conn;
        private $table = 'quotes';

        // Table columns
        public $id;
        public $quote;
        public $author;
        public $category;

        private $select_stmt;

        // Constructor
        public function __construct($db) {
            $this->conn = $db;
            $this->select_stmt = "
                SELECT q.id, quote, author, category
                FROM {$this->table} q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
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

        public function read_one($id) {
            try {
                $query = $this->select_stmt . "WHERE q.id = {$id};";

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

        public function read_all_from_author($author_id) {
            try {
                $query = $this->select_stmt . "WHERE author_id = {$author_id};";

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

        public function read_all_from_author_with_category($author_id, $category_id) {
            try {
                $query = $this->select_stmt . "
                    WHERE author_id = {$author_id}
                    AND category_id = {$category_id};
                ";

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