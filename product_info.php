<?php
    include_once 'includes/dbh.php';

    $stmt = $conn->prepare('SELECT * FROM products WHERE pid = :id');
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($row);
?>