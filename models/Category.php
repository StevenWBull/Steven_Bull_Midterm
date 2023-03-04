<?php 
    class Category {
        // Connect to DB
        private $conn;
        private $table = 'categories';

        // Table columns
        public $id;
        public $category;

        private $select_stmt;

        // Constructor
        public function __construct($db) {
            $this->conn = $db;
            $this->select_stmt = "
                SELECT id, category
                FROM {$this->table} c
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

        public function read_one($category_id) {
            try {
                $query = $this->select_stmt . "WHERE id = :category_id;";

                // Prepare statement
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':category_id', $category_id);

                // Execute Query
                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }

        public function create($category) {
            try {
                // Use this to prevent duplication
                // $check_query = "
                //     SELECT COUNT(*)
                //     FROM {$this->table}
                //     WHERE LOWER(category) = LOWER(:category);
                // ";
                // $check_stmt = $this->conn->prepare($check_query);
                // $check_stmt->bindParam(':category', $category);

                // $check_stmt->execute();

                // $row_count = $check_stmt->fetchColumn();

                // if ($row_count > 0) {
                //     return array('message' => 'Category already exists');
                // }

                $query = "
                    INSERT INTO {$this->table} (category)
                    VALUES (:category)
                    RETURNING id, category;
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':category', $category);

                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }

        public function update($category_id, $category) {
            try {
                $query = "
                    UPDATE {$this->table}
                    SET category = :category
                    WHERE id = :id
                    RETURNING id, category;
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $category_id);
                $stmt->bindParam(':category', $category);
                
                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }

        public function delete($category_id) {
            try {
                $query = "
                    DELETE FROM {$this->table}
                    WHERE id = :id
                    RETURNING id;
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $category_id);

                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    if ($e->getCode() == '23503') { // 23503 is the SQL state for constraint key violation
                        preg_match("/Key \((.*)\)=\((.*)\)/", $e->getMessage(), $matches);
                        $constraint = $matches[1]; // the name of the violated foreign key constraint\
                        return array('message' => "$constraint Conflict");
                    }
                }

                return $stmt;
            } catch(Throwable $e) {
                $msg = $e->getMessage();
                $this->fatal_error($msg);
            }
        }
    }