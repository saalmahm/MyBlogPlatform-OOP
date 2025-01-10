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

$comment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Supprimer le commentaire en utilisant la classe Comment
$comment = new Comment($conn);
if ($comment->deleteComment($comment_id, $user_id)) {
    header('Location: manage_comments.php');
    exit;
} else {
    echo "Error deleting comment.";
}
?>
