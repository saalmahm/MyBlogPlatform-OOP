<?php
session_start();
require_once '/classes/Database.php';
require_once '/classes/Article.php';
require_once '/classes/Comment.php';

$db = new Database();
$conn = $db->connect();

if (isset($_GET['article_id'])) {
    $article_id = intval($_GET['article_id']);
    
    $article = new Article($conn, $article_id);
    $comment = new Comment($conn);

    $article_result = $article->loadArticleById($article_id);
    $comments_result = $comment->getCommentsByArticle($article_id);

    if (!$article_result) {
        echo "<p class='text-red-500 text-center mt-6'>Article introuvable.</p>";
        exit;
    }
} else {
    echo "<p class='text-red-500 text-center mt-6'>Aucun article sélectionné.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h1 class="text-4xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($article->title); ?></h1>
        <p class="text-gray-700 text-lg leading-relaxed"><?= nl2br(htmlspecialchars($article->content)); ?></p>
    </div>

    <div>
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Commentaires</h2>
        <?php if (count($comments_result) > 0): ?>
            <div class="space-y-6">
                <?php foreach ($comments_result as $comment): ?>
                    <div class="bg-white p-5 rounded-lg shadow flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-white font-semibold">
                                <?= strtoupper(substr(htmlspecialchars($comment->getUsername()), 0, 1)); ?>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-800 font-semibold"><?= htmlspecialchars($comment->getUsername()); ?></p>
                            <p class="text-gray-700 mt-1"><?= nl2br(htmlspecialchars($comment->getContent())); ?></p>
                            <p class="text-gray-500 text-sm mt-2"><?= htmlspecialchars($comment->getCreatedAt()); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 mt-4">Aucun commentaire pour cet article.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
