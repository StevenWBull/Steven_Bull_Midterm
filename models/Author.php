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
                SELECT id, author
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
                $query = $this->select_stmt . "WHERE id = :author_id;";

                // Prepare statement
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':author_id', $author_id);

                // Execute Query
                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }

        public function create($author) {
            try {
                // Use this to prevent duplication
                // $check_query = "
                //     SELECT COUNT(*)
                //     FROM {$this->table}
                //     WHERE LOWER(author) = LOWER(:author);
                // ";
                // $check_stmt = $this->conn->prepare($check_query);
                // $check_stmt->bindParam(':author', $author);

                // $check_stmt->execute();

                // $row_count = $check_stmt->fetchColumn();

                // if ($row_count > 0) {
                //     return array('message' => 'Author already exists');
                // }

                $query = "
                    INSERT INTO {$this->table} (author)
                    VALUES (:author)
                    RETURNING id, author;
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':author', $author);

                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }
    }