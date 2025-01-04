<?php
class Database {
    private $dsn = "mysql:host=localhost;dbname=gestionBlog";
    private $username = "root";
    private $password = "hamdi";
    public $conn;

    // Constructor to establish a database connection using PDO
    public function __construct() {
        try {
            $this->conn = new PDO($this->dsn, $this->username, $this->password);
            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Execute a query and return the result
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // Prepare a statement for execution
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    // Close the database connection
    public function close() {
        $this->conn = null;
    }
}
?>
