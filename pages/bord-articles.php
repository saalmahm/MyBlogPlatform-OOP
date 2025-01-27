<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/Admin.php';
require_once '../classes/Article.php';

$db = new Database();
$conn = $db->connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userLoggedIn = true;
$userId = $_SESSION['user_id'];

$admin = new Admin($conn);
$articles = Article::getAllArticles($conn);
$role = $admin->getUserRole($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Articles Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
<header class="flex justify-between p-4">
    <a href="/index.php" id="cars">
        <img src="images/cars.gif" alt="">
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

<div class="flex">
    <aside class="fixed top-0 left-0 w-64 bg-gray-800 text-gray-200 h-screen z-40">
        <div class="p-4 text-center">
            <h2 class="text-2xl font-bold text-white">Manage</h2>
        </div>
        <nav class="mt-6">
            <a href="./bord-articles.php">
                <button class="block w-full px-4 py-2 text-sm hover:bg-blue-600 text-blue-300">Articles</button>
            </a>
            <a href="./dashboard.php">
                <button class="block w-full px-4 py-2 text-sm hover:bg-green-600 text-green-300">Users</button>
            </a>
            <a href="./bord-tags.php">
                <button id="categories-btn" class="block w-full px-4 py-2 text-sm hover:bg-purple-600 text-purple-300">Tags</button>
            </a>
            <a href="statistiques.php"><button class="block w-full px-4 py-2 text-sm hover:bg-yellow-600 text-yellow-300">Statistics</button></a>
        </nav>
    </aside>
    <main class="ml-64 p-4 w-full">
        <div class="flex justify-between">
            <h1 class="text-2xl font-bold mb-4">Articles</h1>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Title</th>
                <th scope="col" class="px-6 py-3">Content</th>
                <th scope="col" class="px-6 py-3">Author</th>
                <th scope="col" class="px-6 py-3">Likes</th> 
                <th scope="col" class="px-6 py-3">Comments</th> 
                <th scope="col" class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($articles as $article) {
                echo '<tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">';
                echo '<td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">' . htmlspecialchars($article['title']) . '</td>';
                echo '<td class="px-6 py-4">' . htmlspecialchars(substr($article['content'], 0, 100)) . '...</td>';
                echo '<td class="px-6 py-4">' . htmlspecialchars($article['username']) . '</td>';
                echo '<td class="px-6 py-4">' . htmlspecialchars($article['like_count']) . ' likes</td>';
                echo '<td class="px-6 py-4"><a href="view-or-manage-comments.php?article_id=' . $article['id'] . '" class="text-blue-600 hover:underline">View or Manage Comments</a></td>';
                echo '<td class="px-6 py-4">';
                echo '<a href="delete-article.php?delete_article_id=' . $article['id'] . '" class="font-medium text-red-600 hover:underline">Delete</a>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
        </div>
    </main>
</div>
</body>
</html>
