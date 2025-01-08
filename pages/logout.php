<?php
session_start();

require_once '/classes/Database.php'; 
require_once '/classes/Authentication.php';  

$db = new Database();
$conn = $db->conn;

// Passer la connexion à la classe Authentication
$auth = new Authentication($conn); 

$auth->logout();
header('Location: ./login.php');
exit;
?>
