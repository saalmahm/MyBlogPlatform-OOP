<?php

class Comment {
    private $conn;
    private $table = 'comments';
    
    private $id;
    private $article_id;
    private $user_id;
    private $content;
    private $created_at;
    private $username;

    // Constructeur
    public function __construct($db) {
        $this->conn = $db;
    }

    // Setter pour les propriétés
    public function setAttributes($id, $article_id, $user_id, $content, $created_at, $username) {
        $this->id = $id;
        $this->article_id = $article_id;
        $this->user_id = $user_id;
        $this->content = $content;
        $this->created_at = $created_at;
        $this->username = $username;
    }

    // Récupérer les commentaires d'un article
    public function getCommentsByArticle($article_id) {
        $query = "
            SELECT 
                comments.id AS comment_id, 
                comments.article_id,
                comments.user_id,
                comments.content AS comment_content, 
                comments.created_at AS comment_created_at, 
                users.username AS comment_author
            FROM 
                comments
            LEFT JOIN 
                users ON comments.user_id = users.id
            WHERE 
                comments.article_id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $article_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $comments = [];

        while ($row = $result->fetch_assoc()) {
            $comment = new Comment($this->conn);
            $comment->setAttributes(
                $row['comment_id'], 
                $row['article_id'], 
                $row['user_id'], 
                $row['comment_content'], 
                $row['comment_created_at'], 
                $row['comment_author']
            );
            $comments[] = $comment;
        }

        return $comments;
    }

    // Ajouter un commentaire
    public function addComment($article_id, $user_id, $content) {
        $query = "INSERT INTO comments (article_id, user_id, content) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iis', $article_id, $user_id, $content);
        return $stmt->execute();
    }

    // Mettre à jour un commentaire
    public function updateComment($comment_id, $content) {
        $query = "UPDATE comments SET content = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $content, $comment_id);
        return $stmt->execute();
    }

    // Supprimer un commentaire
    public function deleteComment($comment_id) {
        $query = "DELETE FROM comments WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $comment_id);
        return $stmt->execute();
    }
    public function getCommentsByUser($user_id) { 
        $query = "SELECT comments.id, comments.content, articles.title FROM comments JOIN articles ON comments.article_id = articles.id WHERE comments.user_id = :user_id";
         $stmt = $this->conn->prepare($query); $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt->execute();
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    // Accesseurs pour les propriétés
    public function getId() { return $this->id; }
    public function getArticleId() { return $this->article_id; }
    public function getUserId() { return $this->user_id; }
    public function getContent() { return $this->content; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUsername() { return $this->username; }
}
?>
