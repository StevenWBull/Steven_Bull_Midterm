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
                $query = $this->select_stmt . "WHERE q.id = :id;";

                // Prepare statement
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);

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
                $query = $this->select_stmt . "WHERE author_id = :author_id;";

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

        public function read_all_from_author_with_category($author_id, $category_id) {
            try {
                $query = $this->select_stmt . "
                    WHERE author_id = :author_id
                    AND category_id = :category_id;
                ";

                // Prepare statement
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':author_id', $author_id);
                $stmt->bindParam(':category_id', $category_id);

                // Execute Query
                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }

        public function create($quote, $author_id, $category_id) {
            try {
                $query = "
                    INSERT INTO {$this->table} (quote, author_id, category_id)
                    VALUES (:quote, :author_id, :category_id)
                    RETURNING id;
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':quote', html_entity_decode($quote));
                $stmt->bindParam(':author_id', $author_id);
                $stmt->bindParam(':category_id', $category_id);

                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    if ($e->getCode() == '23503') { // 23503 is the SQL state for foreign key violation
                        preg_match("/Key \((.*)\)=\((.*)\)/", $e->getMessage(), $matches);
                        $constraint = $matches[1]; // the name of the violated foreign key constraint\
                        return array('message' => "$constraint Not Found");
                    }
                }

                return $this->read_one($stmt->fetchColumn());
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }

        public function update($quote_id, $quote, $author_id, $category_id) {
            try {
                $query = "
                    UPDATE {$this->table}
                    SET quote = :quote,
                        author_id = :author_id,
                        category_id = :category_id
                    WHERE id = :quote_id
                    RETURNING id;
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':quote_id', $quote_id);
                $stmt->bindParam(':quote', html_entity_decode($quote));
                $stmt->bindParam(':author_id', $author_id);
                $stmt->bindParam(':category_id', $category_id);

                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    return null;
                }

                return $this->read_one($stmt->fetchColumn());
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }
    }