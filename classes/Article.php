<?php
class Article {
    private $db;
    private $id;
    private $title;
    private $content;
    private $image;
    private $user_id;

    // Constructor to initialize the database connection and optionally load an article by id
    public function __construct($db, $id = null) {
        $this->db = $db;

        if ($id) {
            $this->loadArticleById($id);
        }
    }

    // Load an article by its id
    public function loadArticleById($id) {
        $sql = "SELECT * FROM articles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            $this->id = $result['id'];
            $this->title = $result['title'];
            $this->content = $result['content'];
            $this->image = $result['image'];
            $this->user_id = $result['user_id'];
            return true;
        } else {
            echo "<p class='text-red-500 text-center mt-6'>Aucun article trouvé avec l'ID $id.</p>";
            return false;
        }
    }
    
    

    // Create a new article
    public function createArticle($title, $content, $image, $user_id) {
        $sql = "INSERT INTO articles (title, content, image, user_id) VALUES (:title, :content, :image, :user_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        } else {
            echo "Erreur d'insertion : " . implode(" - ", $stmt->errorInfo());
            return false;
        }
    }
    

    // Update an existing article
    public function updateArticle($title, $content, $image) {
        $sql = "UPDATE articles SET title = :title, content = :content, image = :image WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Delete an article and its associated tags, likes, and comments
    public function deleteArticle() {
        try {
            $this->db->beginTransaction();
    
            // Suppression des tags associés
            $sql = "DELETE FROM article_tags WHERE article_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
    
            // Suppression des likes associés
            $sql = "DELETE FROM likes WHERE article_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
    
            // Suppression des commentaires associés
            $sql = "DELETE FROM comments WHERE article_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
    
            // Suppression de l'article lui-même
            $sql = "DELETE FROM articles WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
    
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    

    // Add a tag to an article
    public function addTag($article_id, $tag_id) {
        $sql = "INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':tag_id', $tag_id);
        return $stmt->execute();
    }
    

    // Remove a tag from an article
    public function removeTag($tag_id) {
        $sql = "DELETE FROM article_tags WHERE article_id = :article_id AND tag_id = :tag_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':article_id', $this->id);
        $stmt->bindParam(':tag_id', $tag_id);
        return $stmt->execute();
    }

    // Get all articles
        public static function getAllArticles($db) {
            $sql = "
                SELECT articles.*, users.username, GROUP_CONCAT(tags.name SEPARATOR ', ') AS tags,
                       COALESCE(SUM(likes.id), 0) AS like_count
                FROM articles
                JOIN users ON articles.user_id = users.id
                LEFT JOIN article_tags ON articles.id = article_tags.article_id
                LEFT JOIN tags ON article_tags.tag_id = tags.id
                LEFT JOIN likes ON articles.id = likes.article_id
                GROUP BY articles.id
            ";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
 
    

    public function getArticleWithTags($article_id) {
        $sql = "
            SELECT articles.*, GROUP_CONCAT(tags.name SEPARATOR ', ') AS tags 
            FROM articles
            LEFT JOIN article_tags ON articles.id = article_tags.article_id
            LEFT JOIN tags ON article_tags.tag_id = tags.id
            WHERE articles.id = :article_id
            GROUP BY articles.id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getArticlesByUser($user_id) { 
        $sql = "SELECT * FROM articles WHERE user_id = :user_id"; 
        $stmt = $this->db->prepare($sql); 
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
