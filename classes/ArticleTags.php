<?php
class ArticleTags {
    private $db;
    public $article_id;
    public $tag_id;

    // Constructor to initialize the database connection and optionally load an article-tag relationship by article_id and tag_id
    public function __construct($db, $article_id = null, $tag_id = null) {
        $this->db = $db;

        if ($article_id && $tag_id) {
            $this->article_id = $article_id;
            $this->tag_id = $tag_id;
        }
    }

    // Add a tag to an article
    public function addTagToArticle($article_id, $tag_id) {
        $sql = "INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':tag_id', $tag_id);
        return $stmt->execute();
    }

    // Remove a tag from an article
    public function removeTagFromArticle($article_id, $tag_id) {
        $sql = "DELETE FROM article_tags WHERE article_id = :article_id AND tag_id = :tag_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':tag_id', $tag_id);
        return $stmt->execute();
    }

    // Get all tags for a specific article
    public static function getAllTagsForArticle($db, $article_id) {
        $sql = "SELECT tags.* FROM tags
                JOIN article_tags ON tags.id = article_tags.tag_id
                WHERE article_tags.article_id = :article_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all articles associated with a specific tag
    public static function getAllArticlesForTag($db, $tag_id) {
        $sql = "SELECT articles.* FROM articles
                JOIN article_tags ON articles.id = article_tags.article_id
                WHERE article_tags.tag_id = :tag_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':tag_id', $tag_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
