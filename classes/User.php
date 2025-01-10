<?php
class User {
    private $conn;
    public $user_id;
    public $username;
    public $role_id;

    public function __construct($conn, $user_id) {
        $this->conn = $conn;
        $this->user_id = $user_id;
        $this->loadUserData();
    }

    private function loadUserData() {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bindParam(1, $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $this->username = $result['username'];
            $this->role_id = $result['role_id'];
        }
    }

    public function getUserRole() {
        return $this->role_id;
    }

    public function addArticle($title, $content, $tags, $image) {
        $stmt = $this->conn->prepare("INSERT INTO articles (title, content, image, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $title, PDO::PARAM_STR);
        $stmt->bindParam(2, $content, PDO::PARAM_STR);
        $stmt->bindParam(3, $image, PDO::PARAM_STR);
        $stmt->bindParam(4, $this->user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $article_id = $this->conn->lastInsertId();
            $this->addTagsToArticle($article_id, $tags);
        } else {
            echo "Error: " . $stmt->errorInfo();
        }
    }

    private function addTagsToArticle($article_id, $tags) {
        $tag_stmt = $this->conn->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
        foreach ($tags as $tag_id) {
            $tag_stmt->bindParam(1, $article_id, PDO::PARAM_INT);
            $tag_stmt->bindParam(2, $tag_id, PDO::PARAM_INT);
            $tag_stmt->execute();
        }
    }

    public function likeOrUnlikeArticle($article_id) {
        // Check if the user has already liked this article
        $checkLikeQuery = "SELECT * FROM likes WHERE user_id = ? AND article_id = ?";
        $stmt = $this->conn->prepare($checkLikeQuery);
        $stmt->bindParam(1, $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $article_id, PDO::PARAM_INT);
        $stmt->execute();
        $checkLikeResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($checkLikeResult) == 0) {
            // Add like
            $likeQuery = "INSERT INTO likes (user_id, article_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($likeQuery);
            $stmt->bindParam(1, $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $article_id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Remove like
            $unlikeQuery = "DELETE FROM likes WHERE user_id = ? AND article_id = ?";
            $stmt = $this->conn->prepare($unlikeQuery);
            $stmt->bindParam(1, $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $article_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}
?>
