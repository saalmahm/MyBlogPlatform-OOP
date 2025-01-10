<?php
class Admin {
    private $conn;

    // Constructeur qui prend la connexion à la base de données
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Méthode pour obtenir un utilisateur par son ID
    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Retourne l'utilisateur trouvé
    }

    // Méthode pour obtenir tous les rôles
    public function getRoles() {
        $sql = "SELECT * FROM roles";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC); // Retourne tous les rôles
    }

    // Méthode pour mettre à jour le rôle d'un utilisateur
    public function updateUserRole($userId, $roleId) {
        $sql = "UPDATE users SET role_id=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $roleId, $userId);
        return $stmt->execute(); // Retourne true si la mise à jour réussit
    }

    // Méthode pour obtenir un tag par son ID
    public function getTagById($tagId) {
        $sql = "SELECT * FROM tags WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $tagId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Retourne le tag trouvé
    }

    // Méthode pour mettre à jour le nom d'un tag
    public function updateTagName($tagId, $tagName) {
        $sql = "UPDATE tags SET name=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $tagName, $tagId);
        return $stmt->execute(); // Retourne true si la mise à jour réussit
    }

    // Méthode pour obtenir le nombre total d'articles
    public function getTotalArticles() {
        $sql = "SELECT COUNT(*) AS total_articles FROM articles";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_assoc()['total_articles'];
        }
        return 0; 
    }

    // Méthode pour obtenir le nombre total d'utilisateurs
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) AS total_users FROM users";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_assoc()['total_users'];
        }
        return 0; 
    }

    // Méthode pour obtenir le nombre total de tags
    public function getTotalTags() {
        $sql = "SELECT COUNT(DISTINCT tag_id) AS total_tags FROM article_tags";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_assoc()['total_tags'];
        }
        return 0; 
    }

    // Méthode pour récupérer les commentaires d'un article
    public function getCommentsByArticle($articleId) {
        $comment = new Comment($this->conn);
        return $comment->getCommentsByArticle($articleId);
    }

    // Méthode pour supprimer un commentaire
    public function deleteComment($commentId) {
        $comment = new Comment($this->conn);
        return $comment->deleteComment($commentId);
    }

    // Méthode pour obtenir le rôle d'un utilisateur
    public function getUserRole($userId) { 
        $sql = "SELECT role_id FROM users WHERE id = ?";
         $stmt = $this->conn->prepare($sql);
          $stmt->bindParam(1, $userId, PDO::PARAM_INT); 
          $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC); 
           return $row ? $row['role_id'] : null; }

    public function getCommentByIdAndUser($commentId, $userId) {
        $sql = "SELECT * FROM comments WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $commentId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Retourne le commentaire trouvé
    }

    // Méthode pour mettre à jour un commentaire
    public function updateComment($commentId, $newContent) {
        $sql = "UPDATE comments SET content = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $newContent, $commentId);
        return $stmt->execute(); // Retourne true si la mise à jour réussit
    }
    
    public function getAllUsers() {
        $sql = "SELECT users.id, users.username, users.email, roles.name AS role_name FROM users 
                JOIN roles ON users.role_id = roles.id";
        $result = $this->conn->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
}

?>
