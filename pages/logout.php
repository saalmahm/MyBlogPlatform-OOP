<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/Authentication.php';

$db = new Database();
$conn = $db->connect();

$auth = new Authentification($conn);
$auth->logout();

header('Location: ./login.php');
?>
