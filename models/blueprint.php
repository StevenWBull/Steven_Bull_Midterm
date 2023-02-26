<?php 
    class Blueprint {
        // Connect to DB
        private $conn;
        private $table = 'table_name_here';

        // Table columns
        public $id;
        public $created_at;
        public $column;

        // Constructor
        public function __construct($db) {
            $this->conn = $db;
        }

        // Get
        public function read() {
            $query = "SELECT id FROM {$this->table}";

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Execute Query
            $stmt->execute();

            return $stmt;
        }
    }