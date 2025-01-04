<?php
class Comment {
    private $db;
    public $id;
    public $content;
    public $user_id;
    public $article_id;
    public $created_at;

    // Constructor to initialize the database connection and optionally load a comment by id
    public function __construct($db, $id = null) {
        $this->db = $db;

        if ($id) {
            $this->loadCommentById($id);
        }
    }

    // Load a comment by its id
    public function loadCommentById($id) {
        $sql = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->id = $result['id'];
        $this->content = $result['content'];
        $this->user_id = $result['user_id'];
        $this->article_id = $result['article_id'];
        $this->created_at = $result['created_at'];
    }

    // Create a new comment
    public function createComment($content, $user_id, $article_id) {
        $sql = "INSERT INTO comments (content, user_id, article_id) VALUES (:content, :user_id, :article_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':article_id', $article_id);
        return $stmt->execute();
    }

    // Update an existing comment
    public function updateComment($content) {
        $sql = "UPDATE comments SET content = :content WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Delete a comment
    public function deleteComment() {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Get all comments for a specific article
    public static function getAllCommentsForArticle($db, $article_id) {
        $sql = "SELECT comments.*, users.username FROM comments
                JOIN users ON comments.user_id = users.id
                WHERE comments.article_id = :article_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all comments made by a specific user
    public static function getAllCommentsByUser($db, $user_id) {
        $sql = "SELECT comments.*, articles.title AS article_title FROM comments
                JOIN articles ON comments.article_id = articles.id
                WHERE comments.user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
