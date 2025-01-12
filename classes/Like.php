<?php
class Like {
    private $db;
    public $id;
    public $user_id;
    public $article_id;
    public $created_at;

    public function __construct($db, $id = null) {
        $this->db = $db;

        if ($id) {
            $this->loadLikeById($id);
        }
    }

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

    public function addLike($user_id, $article_id) {
        if (!$this->userHasLiked($user_id, $article_id)) {
            $sql = "INSERT INTO likes (user_id, article_id) VALUES (:user_id, :article_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':article_id', $article_id);
            return $stmt->execute();
        }
        return false;
    }

    public function removeLike($user_id, $article_id) {
        $sql = "DELETE FROM likes WHERE user_id = :user_id AND article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':article_id', $article_id);
        return $stmt->execute();
    }

    public function userHasLiked($user_id, $article_id) {
        $sql = "SELECT * FROM likes WHERE user_id = :user_id AND article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    public function toggleLike($user_id, $article_id) {
        if ($this->userHasLiked($user_id, $article_id)) {
            return $this->removeLike($user_id, $article_id);
        } else {
            return $this->addLike($user_id, $article_id);
        }
    }

    public static function getAllLikesForArticle($db, $article_id) {
        $sql = "SELECT * FROM likes WHERE article_id = :article_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countLikes($article_id) {
        $sql = "SELECT COUNT(*) as total_likes FROM likes WHERE article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_likes'];
    }
    
}

?>
