<?php
session_start();
require_once '/classes/Authentication.php';
$auth = new Authentication(null);  
$auth->logout();
header('Location: ./login.php');
exit;
?>
