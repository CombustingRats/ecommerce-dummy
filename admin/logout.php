<?php 
include_once '../includes/dbh.php';
session_start();
session_destroy();

$stmt = $conn->prepare('DELETE FROM auth WHERE token = :token');
$stmt->bindValue(':token', $_COOKIE['auth']);
$stmt->execute();

setcookie("auth", "", 0, "/", $_SERVER['HTTP_HOST'], false, true);

header("Location: ../login.php");
?>