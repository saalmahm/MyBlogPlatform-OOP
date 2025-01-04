<?php
class Authentication {
    private $db;

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Method to handle user login
    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($password, $result['password'])) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            return true;
        } else {
            return false;
        }
    }

    // Method to handle user logout
    public function logout() {
        session_unset();
        session_destroy();
    }

    // Method to verify if a session is active
    public function verifierSession() {
        return isset($_SESSION['user_id']);
    }

    // Method to handle user signup
    public function signup($username, $email, $password) {
        // Check if the username or email already exists
        $sql = "SELECT * FROM users WHERE email = :email OR username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return false; // User already exists
        }

        // Hash the password and insert the new user
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $role_id = 2; // Default user role

        $sql = "INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role_id', $role_id);
        return $stmt->execute();
    }
}
?>
