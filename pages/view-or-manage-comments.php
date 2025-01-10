<?php
session_start();

require_once '/classes/Database.php';
require_once '/classes/Comment.php';

$db = new Database();
$conn = $db->connect();

if (isset($_GET['article_id'])) {
    $article_id = $_GET['article_id'];

    $comment = new Comment($conn);
    $comments = $comment->getCommentsByArticle($article_id);
} else {
    // Rediriger vers la page des articles si aucun article_id n'est trouvé
    header('Location: bord-articles.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View or Manage Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-6">
    <div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-6">Commentaires pour l'article ID : <?php echo $article_id; ?></h2>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Auteur</th>
                    <th scope="col" class="px-6 py-3">Contenu</th>
                    <th scope="col" class="px-6 py-3">Créé le</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        echo '<tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">';
                        echo '<td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . htmlspecialchars($comment->getUsername()) . '</td>';
                        echo '<td class="px-6 py-4">' . htmlspecialchars(substr($comment->getContent(), 0, 100)) . '...</td>';
                        echo '<td class="px-6 py-4">' . htmlspecialchars($comment->getCreatedAt()) . '</td>';
                        echo '<td class="px-6 py-4">';
                        echo '<a href="delete-comment.php?comment_id=' . $comment->getId() . '&article_id=' . $article_id . '" class="font-medium text-red-600 hover:underline">Supprimer</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center px-6 py-4">Aucun commentaire trouvé pour cet article.</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div class="mt-6">
            <a href="bord-articles.php" class="text-blue-600 hover:underline">Retour aux articles</a>
        </div>
    </div>
</body>
</html>
