<?php
require_once 'User.php';

class Admin extends User {
    // Method to add a new tag
    public function addTag($name) {
        $sql = "INSERT INTO tags (name) VALUES (:name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    // Method to delete a tag
    public function deleteTag($tag_id) {
        $sql = "DELETE FROM tags WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $tag_id);
        return $stmt->execute();
    }

    // Method to modify a tag
    public function modifyTag($tag_id, $newName) {
        $sql = "UPDATE tags SET name = :name WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $newName);
        $stmt->bindParam(':id', $tag_id);
        return $stmt->execute();
    }

    // Method to view all tags
    public function viewTags() {
        $sql = "SELECT * FROM tags";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to view all users
    public function viewUsers() {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to delete a user
    public function deleteUser($user_id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // Method to modify user role
    public function modifyUserRole($user_id, $newRole) {
        $sql = "UPDATE users SET role_id = :role_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role_id', $newRole);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // Method to view all articles
    public function viewArticles() {
        $sql = "SELECT * FROM articles";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to delete any article
    public function deleteAnyArticle($article_id) {
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $article_id);
        return $stmt->execute();
    }

    // Method to view statistics
    public function viewStatistics() {
        $statistics = [];

        // Get total articles
        $sql = "SELECT COUNT(*) AS total_articles FROM articles";
        $stmt = $this->db->query($sql);
        $statistics['total_articles'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_articles'];

        // Get total users
        $sql = "SELECT COUNT(*) AS total_users FROM users";
        $stmt = $this->db->query($sql);
        $statistics['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

        // Get total tags
        $sql = "SELECT COUNT(*) AS total_tags FROM tags";
        $stmt = $this->db->query($sql);
        $statistics['total_tags'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_tags'];

        return $statistics;
    }

    // Method to view all comments
    public function viewComments() {
        $sql = "SELECT * FROM comments";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to delete any comment
    public function deleteComment($comment_id) {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $comment_id);
        return $stmt->execute();
    }
}
?>
