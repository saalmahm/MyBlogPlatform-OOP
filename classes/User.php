<?php
class User {
    private $db;
    public $id;
    public $username;
    public $email;
    public $password;
    public $role_id;

    // Constructor to initialize the database connection and optionally load a user by id
    public function __construct($db, $id = null) {
        $this->db = $db;

        if ($id) {
            $this->loadUserById($id);
        }
    }

    // Load a user by their id
    public function loadUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->id = $result['id'];
        $this->username = $result['username'];
        $this->email = $result['email'];
        $this->password = $result['password'];
        $this->role_id = $result['role_id'];
    }

    // Update the role of a user
    public function updateUserRole($role_id) {
        $sql = "UPDATE users SET role_id = :role_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Check if the user can log in
    public function canLogin($usernameOrEmail, $password) {
        $sql = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usernameOrEmail', $usernameOrEmail);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        return false;
    }

    // User logout
    public function logout() {
        session_unset();
        session_destroy();
    }
}
?>
