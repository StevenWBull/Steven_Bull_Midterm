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

        // Constructor
        public function __construct($db) {
            $this->conn = $db;
        }

        // Get
        public function read_all() {
            try {
                $query = "
                    SELECT q.id, quote, author, category
                    FROM {$this->table} q
                    INNER JOIN authors a ON a.id = q.author_id
                    INNER JOIN categories c ON c.id = q.category_id;
                ";

                // Prepare statement
                $stmt = $this->conn->prepare($query);

                // Execute Query
                $stmt->execute();

                return $stmt;
            } catch(Throwable $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
    }