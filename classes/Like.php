<?php
class Like {
    private $db;
    public $id;
    public $user_id;
    public $article_id;
    public $created_at;

    // Constructor to initialize the database connection and optionally load a like by id
    public function __construct($db, $id = null) {
        $this->db = $db;

        if ($id) {
            $this->loadLikeById($id);
        }
    }

    // Load a like by its id
    public function loadLikeById($id) {
        $sql = "SELECT * FROM likes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->id = $result['id'];
        $this->user_id = $result['user_id'];
        $this->article_id = $result['article_id'];
        $this->created_at = $result['created_at'];
    }

    // Add a like to an article
    public function addLike($user_id, $article_id) {
        $sql = "INSERT INTO likes (user_id, article_id) VALUES (:user_id, :article_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':article_id', $article_id);
        return $stmt->execute();
    }

    // Remove a like from an article
    public function removeLike($user_id, $article_id) {
        $sql = "DELETE FROM likes WHERE user_id = :user_id AND article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':article_id', $article_id);
        return $stmt->execute();
    }

    // Check if a user has liked an article
    public function userHasLiked($user_id, $article_id) {
        $sql = "SELECT * FROM likes WHERE user_id = :user_id AND article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Get all likes for an article
    public static function getAllLikesForArticle($db, $article_id) {
        $sql = "SELECT * FROM likes WHERE article_id = :article_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
