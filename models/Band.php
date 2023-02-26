<?php 
    class Band {
        // Connect to DB
        private $conn;
        private $table = 'bands';

        // Table columns
        public $id;
        public $created_at;
        public $name;
        public $founding_year;
        public $genre_name;

        // Constructor
        public function __construct($db) {
            $this->conn = $db;
        }

        // Get
        public function read() {
            try {
                $query = "
                    SELECT b.id, created_at, name, founding_year, genre_name
                    FROM {$this->table} b
                    INNER JOIN genres g ON genre_id = g.id;
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