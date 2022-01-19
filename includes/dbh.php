<?php

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "assignment";

//$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
$conn = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);