<?php
class Authentification {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function register($username, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $checkQuery = "SELECT * FROM users WHERE email = :email OR username = :username";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return "L'utilisateur existe déjà.";
        }

        $insertQuery = "INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, 2)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHash);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Erreur lors de l'inscription : " . $stmt->errorInfo()[2];
        }
    }

    public function login($usernameOrEmail, $password) {
        $query = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usernameOrEmail', $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if (password_verify($password, $result['password'])) {
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
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
