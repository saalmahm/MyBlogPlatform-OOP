<?php
session_start();
require_once '/classes/Database.php';
require_once '/classes/Article.php';
require_once '/classes/Comment.php';

$db = new Database();
$conn = $db->connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['delete_article_id'])) {
    $articleId = $_GET['delete_article_id'];

    $article = new Article($conn, $articleId);
    
    if ($article->deleteArticle()) {
        echo "Article, commentaires, likes et tags supprimés avec succès.";
        header("Location: bord-articles.php");
        exit;
    } else {
        echo "Error: Unable to delete article.";
    }
} else {
    echo "Aucun article sélectionné pour la suppression.";
}
?>
