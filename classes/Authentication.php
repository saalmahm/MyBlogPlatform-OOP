<?php
class Authentification {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function register($username, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $checkQuery = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return "L'utilisateur existe déjà.";
        }

        $insertQuery = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, 2)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param("sss", $username, $email, $passwordHash);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Erreur lors de l'inscription : " . $stmt->error;
        }
    }

    public function login($usernameOrEmail, $password) {
        $query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            } else {
                return "Mot de passe incorrect.";
            }
        } else {
            return "Utilisateur non trouvé.";
        }
    }

    public function logout() {
        session_unset(); 
        session_destroy();
        return true;  
    }
}

?>
