<?php
session_start();
require_once '/classes/Database.php';
require_once '/classes/Article.php';
require_once '/classes/ArticleTags.php';

$db = new Database();
$conn = $db->connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

if (isset($_GET['id'])) {
    $article_id = $_GET['id'];
    
    $article = new Article($conn);
    $articleTags = new ArticleTags($conn);

    $article_result = $article->loadArticleById($article_id);
    
    if ($article_result) {
        $articleTags->removeTagFromArticle($article_id, $tag_id); // Suppression des tags associés
        if ($article->deleteArticle($article_id)) {
            header('Location: ./profile.php'); 
            exit;
        } else {
            echo "Erreur lors de la suppression de l'article.";
        }
    } else {
        echo "Article introuvable.";
    }
} else {
    header('Location: ./profile.php'); 
}
?>
