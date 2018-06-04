<?php

    class db {
        
        private $host;
        private $db;
        private $user;
        private $pass;
        private $charset;
        
        public $pdo;
        
        // Connect to the database.  $pdo can be used thoughout this class.
        function __construct() {
            
            // Database details
            $this->host = 'x.x.x.x';
            $this->db   = 'database_name';
            $this->user = 'database_user';
            $this->pass = 'database_pass';
            $this->charset = 'utf8';

            // Connect to the database and create a new PDO object ($pdo)
            $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
            $opt = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            try {
                $this->pdo = new PDO($dsn, $this->user, $this->pass, $opt);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
            
        }

    }

?>