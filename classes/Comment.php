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
                    comments.content, 
                    comments.created_at, 
                    users.username
                FROM 
                    comments
                LEFT JOIN 
                    users ON comments.user_id = users.id
                WHERE 
                    comments.article_id = :article_id
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }    

    // Ajouter un commentaire
    public function addComment($article_id, $user_id, $content) {
        $query = "INSERT INTO comments (article_id, user_id, content) VALUES (:article_id, :user_id, :content)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Mettre à jour un commentaire
    public function updateComment($comment_id, $content) {
        $query = "UPDATE comments SET content = :content WHERE id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        $stmt->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Supprimer un commentaire
    public function deleteComment($comment_id) {
        $query = "DELETE FROM comments WHERE id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Récupérer les commentaires d'un utilisateur
    public function getCommentsByUser($user_id) {
        $query = "SELECT comments.*, articles.title FROM comments 
                  JOIN articles ON comments.article_id = articles.id
                  WHERE comments.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
