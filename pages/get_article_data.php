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
    $articleDetails = $article->getArticleWithTags($article_id);

    if ($articleDetails) {
        echo json_encode($articleDetails);
    } else {
        echo json_encode([]);
    }
}
?>
