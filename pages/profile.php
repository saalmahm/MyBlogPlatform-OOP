<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/Article.php';
require_once '../classes/Tag.php';
require_once '../classes/Like.php';
require_once '../classes/User.php';
require_once '../classes/Admin.php';

$db = (new Database())->connect();
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'];

$admin = new Admin($db);
$currentUser = $admin->getUserById($user_id);
$username = $currentUser['username'];
$role = $currentUser['role_id'];

$articleObj = new Article($db);
$tagObj = new Tag($db);
$likeObj = new Like($db);
$tags = $tagObj->getAllTags($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $tags = $_POST['tags'] ?? []; // Par défaut, un tableau vide
        $user_id = $_SESSION['user_id'];

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image']['name'];
            $target_dir = '../uploads/';
            $target_file = $target_dir . basename($image);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $article_id = $articleObj->createArticle($title, $content, $image, $user_id);

                if ($article_id) {
                    foreach ($tags as $tag_id) {
                        $articleObj->addTag($article_id, $tag_id); // Passe l'ID de l'article à addTag
                    }
                    echo "<p class='text-green-500'>Article ajouté avec succès !</p>";
                } else {
                    echo "<p class='text-red-500'>Erreur lors de l'ajout de l'article.</p>";
                }
            } else {
                echo "<p class='text-red-500'>Erreur lors du téléchargement de l'image.</p>";
            }
        } else {
            echo "<p class='text-red-500'>Veuillez télécharger une image valide.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='text-red-500'>Erreur : " . $e->getMessage() . "</p>";
    }
}

// Gestion des likes
if ($userLoggedIn && isset($_GET['like'])) {
    $article_id = $_GET['like'];
    $likeObj->toggleLike($article_id, $user_id);
}

// Récupérer les articles de l'utilisateur
$articles = $articleObj->getArticlesByUser($user_id);
$tags = Tag::getAllTags($db); // Récupère tous les tags ici
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<header class="flex justify-between p-4 fixed top-0 left-0 right-0 bg-white shadow-md z-50">
    <a href="/home.php" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
        <img src="/images/icon.png" class="h-8" alt="Flowbite Logo" />
        <span class="text-2xl font-bold whitespace-nowrap dark:text-gray-500">MyBlogPlatform</span>
    </a>
    <div class="lg:hidden" id="burger-icon">
        <img src="/images/menu.png" alt="Menu">
    </div>
    <div id="sidebar"
        class="shadow-xl fixed top-0 right-0 w-1/3 h-full bg-gray-200 z-50 transform translate-x-full duration-300">
        <div class="flex justify-end p-4">
            <button id="close-sidebar" class="text-3xl">X</button>
        </div>
        <div class="flex flex-col items-center space-y-4 text-white">
            <a href="/home.php" class="text-black text-lg">Home</a>
            <a href="/index.php" class="text-black text-lg">Blog</a>
            <?php if ($userLoggedIn): ?>
                <?php if ($role == 1): ?> <!-- Admin role -->
                    <a href="/pages/dashboard.php" class="text-black text-lg">Dashboard</a>
                <?php endif; ?>
                <?php if ($role == 2): ?> <!-- User role -->
                    <a href="/pages/profile.php" class="text-black text-lg">Profile</a>
                <?php endif; ?>
                <a href="/pages/logout.php" class="text-red-500 text-lg">Log out</a>
            <?php else: ?>
                <a href="/pages/signup.php" class="text-blue-500 text-lg">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hidden lg:flex justify-center space-x-4">
        <ul class="flex items-center text-sm font-medium text-gray-400 mb-0">
            <li>
                <a href="/home.php" class="hover:underline me-4 md:me-6">Home</a>
            </li>
            <li>
                <a href="/index.php" class="hover:underline me-4 md:me-6">Blog</a>
            </li>
            <?php if ($userLoggedIn): ?>
                <?php if ($role == 1): ?> <!-- Admin role -->
                    <li>
                        <a href="/pages/dashboard.php" class="hover:underline me-4 md:me-6">Dashboard</a>
                    </li>
                <?php endif; ?>
                <?php if ($role == 2): ?> <!-- User role -->
                    <li>
                        <a href="/pages/profile.php" class="hover:underline me-4 md:me-6">Profile</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="/pages/logout.php" class="text-red-500 hover:underline me-4 md:me-6">Log out</a>
                </li>
            <?php else: ?>
                <li>
                    <a href="/pages/signup.php" class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 me-4 md:me-6">Sign Up</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</header>
<section class="bg-gradient-to-r from-blue-200 via-blue-300 to-blue-400 py-8 relative flex justify-between items-center mt-20 rounded-lg shadow-lg">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full px-6">
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-800 mb-4 sm:mb-0">
            <?php echo "Welcome, " . htmlspecialchars($currentUser['username']); ?>
        </h1>
        <a href="manage_comments.php" class="mt-4 sm:mt-0 inline-block text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-6 py-2 font-medium text-lg hover:underline transition duration-300 ease-in-out shadow-md">
            View or Manage Your Comments
        </a>
    </div>
</section>

<section>
    <div class="container mx-auto px-4 mt-10"> 
        <div class="flex mb-8">
            <button class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700" onclick="openModal()">            
                <span class="relative px-6 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                    + Add an article
                </span>
            </button>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 mt-10">
            <?php 
            $articles = Article::getAllArticles($db);
            foreach ($articles as $article): ?>
                <div class='bg-gray-100 rounded-lg shadow-md p-4'>
                    <div class='flex justify-between'>
                        <h3 class='text-xl font-bold mb-2 '><?php echo htmlspecialchars($article['title']); ?></h3>
                        <div class='flex'>
                            <button class='text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800'>
                                <a href='edit_article.php?id=<?php echo htmlspecialchars($article['id']); ?>'>Edit</a>
                            </button>
                            <button class='text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'>
                                <a href='delete_article.php?id=<?php echo htmlspecialchars($article['id']); ?>'>Delete</a>
                            </button>
                        </div>
                    </div>
                    <p class='text-gray-700 mb-4'><?php echo htmlspecialchars($article['content']); ?></p>
                    <img src='/uploads/<?php echo htmlspecialchars($article['image']); ?>' alt="Image de l'article" class='w-full h-48 object-cover mb-4 rounded-lg'>
                    <p class='text-gray-600 text-sm'>Par <?php echo htmlspecialchars($article['username'] ?? 'Utilisateur inconnu'); ?> le <?php echo htmlspecialchars($article['created_at'] ?? 'Date inconnue'); ?></p>
                    <p class='text-blue-600 text-sm'>Tags : <?php echo htmlspecialchars($article['tags'] ?? 'Aucun tag'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<div id="modal" class="fixed z-10 inset-0 overflow-y-auto mt-6 hidden ">        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Add an Article
                            </h3>
                            <div class="mt-2">
<form id="articleForm" method="POST" enctype="multipart/form-data">
    <div class="mb-4">
        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
        <input type="text" id="title" name="title" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
    </div>
    <div class="mb-4">
        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
        <textarea id="content" name="content" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required></textarea>
    </div>
    <div class="mb-4">
        <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
        <input type="file" id="image" name="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" required>
    </div>
    <div class="mb-4">
        <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
        <select id="tags" name="tags[]" multiple class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            <?php
            foreach ($tags as $tag) {
                echo "<option value='" . htmlspecialchars($tag['id']) . "'>" . htmlspecialchars($tag['name']) ."</option>";
            }
            ?>
        </select>
        <small class="text-gray-500">Hold Ctrl (or Command on Mac) to select multiple tags.</small>
    </div>
    <div class="sm:flex sm:items-center">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Add Article
        </button>
        <button type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100" onclick="closeModal()">
            Cancel
        </button>
    </div>
</form>

</div>
</div>
 </div>
</div>
 </div>
 </div>
 </div>
 


 <script>
    const menu = document.getElementById("burger-icon");
    const sidebar = document.getElementById("sidebar");
    const closeSidebar = document.getElementById("close-sidebar");

    menu.addEventListener("click", () => {
        sidebar.classList.remove("translate-x-full");  
        sidebar.classList.add("translate-x-0");
    });

    closeSidebar.addEventListener("click", () => {
        sidebar.classList.add("translate-x-full");    
        sidebar.classList.remove("translate-x-0");   
    });
    function openModal() {
            const modal = document.getElementById('modal');
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.classList.add('hidden');
        }
  
</script>
</body>
</html>
