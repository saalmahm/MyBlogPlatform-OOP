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
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $this->username = $user['username'];
            $this->role_id = $user['role_id'];
        }
    }

    public function addArticle($title, $content, $tags, $image) {
        $stmt = $this->conn->prepare("INSERT INTO articles (title, content, image, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $content, $image, $this->user_id);
        if ($stmt->execute()) {
            $article_id = $stmt->insert_id;
            $this->addTagsToArticle($article_id, $tags);
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    private function addTagsToArticle($article_id, $tags) {
        $tag_stmt = $this->conn->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
        foreach ($tags as $tag_id) {
            $tag_stmt->bind_param("ii", $article_id, $tag_id);
            $tag_stmt->execute();
        }
    }

    public function likeOrUnlikeArticle($article_id) {
        // Check if the user has already liked this article
        $checkLikeQuery = "SELECT * FROM likes WHERE user_id = ? AND article_id = ?";
        $stmt = $this->conn->prepare($checkLikeQuery);
        $stmt->bind_param("ii", $this->user_id, $article_id);
        $stmt->execute();
        $checkLikeResult = $stmt->get_result();

        if ($checkLikeResult->num_rows == 0) {
            // Add like
            $likeQuery = "INSERT INTO likes (user_id, article_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($likeQuery);
            $stmt->bind_param("ii", $this->user_id, $article_id);
            $stmt->execute();
        } else {
            // Remove like
            $unlikeQuery = "DELETE FROM likes WHERE user_id = ? AND article_id = ?";
            $stmt = $this->conn->prepare($unlikeQuery);
            $stmt->bind_param("ii", $this->user_id, $article_id);
            $stmt->execute();
        }
    }
}
?>
