<?php
session_start();
require_once '/classes/Database.php';
require_once '/classes/Comment.php';

$db = new Database();
$conn = $db->connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['comment_id']) && isset($_GET['article_id'])) {
    $comment_id = $_GET['comment_id'];
    $article_id = $_GET['article_id'];

    $comment = new Comment($conn);
    
    if ($comment->deleteComment($comment_id)) {
        // Rediriger vers la page de gestion des commentaires de l'article après suppression
        header('Location: view-or-manage-comments.php?article_id=' . $article_id);
        exit();
    } else {
        echo "Error: Unable to delete comment.";
    }
} else {
    // Rediriger vers la page des articles si les ID ne sont pas trouvés
    header('Location: bord-articles.php');
    exit();
}
?>
