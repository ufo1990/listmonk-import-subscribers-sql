<?php
/*
Database connection
*/

class Database {
	// Database credentials
    private $dbHost;
    private $dbName;
    private $dbUsername;
    private $dbPassword;
    private $conn;
	
	// Constructor - Initializes database configuration
	public function __construct($config) {
        $this->dbHost = $config['db_host'];
        $this->dbName = $config['db_name'];
        $this->dbUsername = $config['db_username'];
        $this->dbPassword = $config['db_password'];
    }
	
	// Establishes a connection to the database
	public function connect() {
        $this->conn = null; // Initialize connection as null
		
        try {
			// Create a new PDO connection
            $this->conn = new PDO("mysql:host=" . $this->dbHost . ";dbname=" . $this->dbName, $this->dbUsername, $this->dbPassword);
            
			// Set PDO error mode to exception for better error handling
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
			// Ensure UTF-8 character encoding for database operations
			$this->conn->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch(PDOException $e) {
			// Log the error message instead of displaying it for security reasons
			error_log($e->getMessage());     
        }
		
		// Return the database connection or null if an error occurred
        return $this->conn;
    }
}