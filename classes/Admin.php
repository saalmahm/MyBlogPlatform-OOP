<?php
class Admin extends User {
    // Constructeur qui prend la connexion à la base de données
    private function __construct($conn, $user_id) {
        parent::__construct($conn, $user_id); // Appelle le constructeur de la classe parent (User)
    }

    // Méthode pour obtenir un utilisateur par son ID
    private function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'utilisateur trouvé
    }

    // Méthode pour obtenir tous les rôles
    private function getRoles() {
        $sql = "SELECT * FROM roles";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les rôles
    }

    // Méthode pour mettre à jour le rôle d'un utilisateur
    private function updateUserRole($userId, $roleId) {
        $sql = "UPDATE users SET role_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $roleId, PDO::PARAM_INT);
        $stmt->bindParam(2, $userId, PDO::PARAM_INT);
        return $stmt->execute(); // Retourne true si la mise à jour réussit
    }

    // Méthode pour obtenir un tag par son ID
    private function getTagById($tagId) {
        $sql = "SELECT * FROM tags WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $tagId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne le tag trouvé
    }

    // Méthode pour mettre à jour le nom d'un tag
    private function updateTagName($tagId, $tagName) {
        $sql = "UPDATE tags SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $tagName, PDO::PARAM_STR);
        $stmt->bindParam(2, $tagId, PDO::PARAM_INT);
        return $stmt->execute(); // Retourne true si la mise à jour réussit
    }

    // Méthode pour obtenir le nombre total d'articles
    private function getTotalArticles() {
        $sql = "SELECT COUNT(*) AS total_articles FROM articles";
        $stmt = $this->conn->query($sql);
        if ($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC)['total_articles'];
        }
        return 0; 
    }

    // Méthode pour obtenir le nombre total d'utilisateurs
    private function getTotalUsers() {
        $sql = "SELECT COUNT(*) AS total_users FROM users";
        $stmt = $this->conn->query($sql);
        if ($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
        }
        return 0; 
    }

    // Méthode pour obtenir le nombre total de tags
    private function getTotalTags() {
        $sql = "SELECT COUNT(DISTINCT tag_id) AS total_tags FROM article_tags";
        $stmt = $this->conn->query($sql);
        if ($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC)['total_tags'];
        }
        return 0; 
    }

    // Méthode pour récupérer les commentaires d'un article
    private function getCommentsByArticle($articleId) {
        $comment = new Comment($this->conn);
        return $comment->getCommentsByArticle($articleId);
    }

    // Méthode pour supprimer un commentaire
    private function deleteComment($commentId) {
        $comment = new Comment($this->conn);
        return $comment->deleteComment($commentId);
    }

    // Méthode pour obtenir le rôle d'un utilisateur
    private function getUserRole($userId) { 
        $sql = "SELECT role_id FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['role_id'] : null; 
    }

    // Méthode pour obtenir tous les utilisateurs avec leurs rôles
    private function getAllUsers() {
        $sql = "SELECT users.id, users.username, users.email, roles.name AS role_name FROM users 
                JOIN roles ON users.role_id = roles.id";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCommentByIdAndUser($commentId, $userId) {
        $sql = "SELECT * FROM comments WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $commentId, PDO::PARAM_INT);
        $stmt->bindParam(2, $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne le commentaire trouvé
    }

    // Méthode pour mettre à jour un commentaire
    private function updateComment($commentId, $newContent) {
        $sql = "UPDATE comments SET content = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $newContent, PDO::PARAM_STR);
        $stmt->bindParam(2, $commentId, PDO::PARAM_INT);
        return $stmt->execute(); // Retourne true si la mise à jour réussit
    }
}

?>
