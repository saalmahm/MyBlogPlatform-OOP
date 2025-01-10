<?php
session_start();

include("/classes/Database.php");
include("/classes/Admin.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Créer une instance de la classe Database et se connecter
$database = new Database();
$conn = $database->connect();

// Créer une instance de la classe Admin
$admin = new Admin($conn);

// Récupérer le commentaire à modifier
$comment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Utiliser la méthode de Admin pour récupérer le commentaire
$comment = $admin->getCommentByIdAndUser($comment_id, $user_id);

if (!$comment) {
    echo "Comment not found or you do not have permission to edit it.";
    exit;
}

// Mettre à jour le commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_content = $_POST['content'];
    
    // Utiliser la méthode de Admin pour mettre à jour le commentaire
    if ($admin->updateComment($comment_id, $new_content)) {
        header('Location: manage_comments.php');
        exit;
    } else {
        echo "Error updating comment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<section class="mt-10 px-6 flex justify-center">
    <div class="w-full max-w-2xl p-6 bg-white border border-gray-300 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-gray-800 text-center">Edit Comment</h1>
        <form method="POST" class="mt-6">
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-600">Your Comment</label>
                <textarea id="content" name="content" class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" rows="5"><?php echo htmlspecialchars($comment['content']); ?></textarea>
            </div>
            <div class="flex mt-6">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 mr-4">
                    Save Changes
                </button>
                <button type="button" onclick="window.history.back();" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</section>
</body>
</html>
