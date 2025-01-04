<?php
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "hamdi";
    private $dbname = "gestionBlog";
    public $conn;

    // Constructor to establish a database connection
    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
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
        $this->conn->close();
    }
}
?>
