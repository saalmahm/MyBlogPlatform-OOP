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

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $article_id = $_GET['id'];

    $article = new Article($conn, $article_id);
    $article->loadArticleById($article_id);

    if (!$article->id) {
        echo "Article not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $article_id = $_POST['id'];
    $tags = $_POST['tags'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $target_dir = '../uploads/';
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $article->updateArticle($title, $content, $image);
        } else {
            echo "Error uploading the image.";
            exit;
        }
    } else {
        $article->updateArticle($title, $content);
    }

    $articleTags = new ArticleTags($conn);
    $articleTags->removeTagFromArticle($article_id, $tag_id); // Suppression des tags associés
    foreach ($tags as $tag_id) {
        $articleTags->addTagToArticle($article_id, $tag_id); // Ajout des nouveaux tags
    }

    header("Location: profile.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css"  rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h1 class="text-2xl font-bold mb-6">Edit Article</h1>
    <form method="POST" enctype="multipart/form-data" id="articleForm" class="space-y-4">
        <input type="hidden" name="id" value="<?php echo $article->id; ?>">
    
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article->title); ?>" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
            <textarea id="content" name="content" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required><?php echo htmlspecialchars($article->content); ?></textarea>
        </div>

        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
            <input type="file" id="image" name="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
        </div>

        <div class="mb-4">
            <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
            <select name="tags[]" id="tags" multiple class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <?php
                // Fetch tags for the current article
                $selected_tags = $articleTags->getAllTagsForArticle($conn, $article_id);
                $selected_tag_ids = array_map(function($tag) { return $tag['id']; }, $selected_tags);

                $query = "SELECT * FROM tags";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    $selected = in_array($row['id'], $selected_tag_ids) ? 'selected' : '';
                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
            <small class="text-gray-500">Hold Ctrl (or Command on Mac) to select multiple tags.</small>
        </div>

        <div class="sm:flex sm:items-center">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Update Article
            </button>
            <a href="profile.php" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                Cancel
            </a>
        </div>
    </form>
</div>

</body>
</html>
