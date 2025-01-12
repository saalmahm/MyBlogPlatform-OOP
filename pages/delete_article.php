<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/Article.php';
require_once '../classes/ArticleTags.php';

$db = new Database();
$conn = $db->connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

if (isset($_GET['id'])) {
    $article_id = $_GET['id'];
    
    $article = new Article($conn, $article_id);
    $articleTags = new ArticleTags($conn);

    $article->loadArticleById($article_id);
    
    if ($article->id) {
        $articleTags->removeAllTagsFromArticle($article_id); // Suppression des tags associés
        if ($article->deleteArticle()) {
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
