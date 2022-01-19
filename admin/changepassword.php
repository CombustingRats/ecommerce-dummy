<?php
// $password = '5678';
// echo password_hash($password, PASSWORD_BCRYPT);
    include_once '../includes/dbh.php';
    include_once '../includes/authorization.php';
    session_start();
    
    if (!isset($_COOKIE['auth'])) {
        header("Location: login.php");
        return;
    }

    $auth = $_COOKIE['auth'];
    $stmt = $conn->prepare("SELECT u.admin FROM auth a JOIN users u ON a.userid = u.userid WHERE a.token = :token");
    $stmt->bindValue(':token', $auth);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $admin = $row['admin'];
        // admin = 0 or 1
        if ($admin <= 0) {
            header("Location: login.php");
            return;
        }
    } else {
        // No record
        header("Location: login.php");
        return;
    }

    if(empty($_SESSION['CSRF'])){
        $_SESSION['CSRF'] = bin2hex(random_bytes(32));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['CSRF'], $_POST['CSRF'])) {
        unset($_SESSION['CSRF']);
        
        $stmt = $conn->prepare("SELECT u.password, u.userid FROM auth a JOIN users u ON a.userid = u.userid WHERE a.token = :token");
        $stmt->bindValue(':token', $_COOKIE['auth']);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $userid = $row['userid'];
        $hash = $row['password'];
        $password = $_POST['old_password'];

        if (password_verify($password, $hash)) {
            $newpassword = $_POST['new_password'];
            $confirmpassword = $_POST['confirm_password'];
            if (empty($newpassword)) {
                $error = "CCC";
            } else if ($newpassword === $confirmpassword) {
                $newhash = password_hash($newpassword, PASSWORD_BCRYPT);
                $stmt = $conn->prepare('UPDATE users SET password = :password WHERE userid = :userid');
                $stmt->bindValue(':password', $newhash);
                $stmt->bindValue(':userid', $userid);
                $stmt->execute();
                header('Location: logout.php');
            } else {
                // Two new passwords does not match
                $error = "AAA";
            }
        } else {
            // Incorrect original password
            $error = "BBB";
        }
        
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/utilities.css">
</head>
<body>
    <section class="login">
        <div class="container grid">
            <div class="admin-panel card">
                <h2>Change Your Password</h2>
                <?php if (isset($error)) : ?>
                <div style="color:red"><?= $error ?></div>
                <?php endif; ?>
                <form action="changepassword.php" method="POST">
                    <input type="hidden" name="CSRF" value="<?= $_SESSION['CSRF'] ?>">
                    <div class="form-control"> <label><h2>Old Password</h2></label> <input type="password" name="old_password" placeholder="Enter your old password..." required> </div>
                    <div class="form-control"> <label><h2>New Password</h2></label> <input type="password" name="new_password" placeholder="Enter your new password..." required> </div>
                    <div class="form-control"> <label><h2>Confirm Password</h2></label> <input type="password" name="confirm_password" placeholder="Reenter your new password..." required> </div>
                    <input type="submit" value="Submit"></input>
                </form>
            </div>
        </div>
    </section>
</body>
</html>