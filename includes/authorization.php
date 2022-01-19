<?php

// Check if the user has their auth cookie set. If not, the user is not authorized. bounce them back to the login page
if (!isset($_COOKIE['auth'])) {
    header("Location: /login.php");
    return;
}

// Select the DB entry where the session token match that stored in the cookie. 
// join the databases on userid so we can retrieve the admin status
$stmt = $conn->prepare("SELECT * FROM `auth` a JOIN `users` u on a.userid = u.userid WHERE token = :token");
$stmt->bindValue(":token", $_COOKIE['auth']);
$stmt->execute();

if($rowAuth = $stmt->fetch(PDO::FETCH_ASSOC)){
    $admin = $rowAuth['admin'];
    if($admin < 1){
        // There is a user but they are not an admin, bounce them back to the login page
        header("Location: /login.php");
        return;
    }
} else {
    // There is a cookie but it does not match anything in the database, redirect to login
    header("Location: /login.php");
    return;
}