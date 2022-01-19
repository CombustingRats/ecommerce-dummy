<?php
    include_once 'includes/dbh.php';
    session_start(); // although we are doing the SID manually, we need the session to store the one time CSRF token
    
    if(empty($_SESSION['CSRF'])){
        /*
        Generate the CSRF token and store it in the session superglobal once the form loads
        */
        $_SESSION['CSRF'] = bin2hex(random_bytes(32));
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['CSRF'], $_POST['CSRF'])) {
        /*
        To prevent CSRF, check if the session CSRF token is equal to the one stored in the session for this request
        Since the token is stored on the server, attackers will not know the csrf and cannot make their own request on your
        session successfully. Unset the token right after to ensure there is only one per form submission
        */
        unset($_SESSION['CSRF']);
        /*
        Get the form details from the login and lookup the user database for the corresponding user that is logging in
        */
        $email = $_POST['email'];
        $password = $_POST['password'];
        $stmt = $conn->prepare('SELECT password, admin, userid FROM users WHERE email=:email');
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hash = $row['password'];
        $admin = $row['admin'];
        $userid = $row['userid'];

        if (password_verify($password, $hash)) {
            /*
            Check if the password is valid and set the session cookie. 
            We set a new session cookie everytime someone logs in to prevent session fixation attacks
            */
            /*$cookie_options = array (
                'expires' => time() + 60*60*24*3,
                'path' => '/',
                'httponly' => true,
                'secure' => false
            );*/
            $auth = bin2hex(random_bytes(32));
            setcookie("auth", $auth, time() + 60*60, "/", $_SERVER['HTTP_HOST'], false, true);

            // session_unset();
            // session_regenerate_id(true);

            // Insert the current session ID into the database, to use it for authentication on other pages
            $stmt = $conn->prepare('INSERT INTO auth (token, userid) VALUES (:token, :userid)');
            $stmt->bindValue(':token', $auth);
            $stmt->bindValue(':userid', $userid);
            $stmt->execute();

            if ($admin > 0) {
                /*
                Checks if the user is an admin, before redirecting them to the admin panel. Otherwise, direct to homepage
                */
                // $_SESSION['admin'] = $admin;
                header('Location: admin/category.php');
            } else {
                header('Location: index.php');
            }
        } else {
            echo "Error Logging in";
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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/utilities.css">
</head>
<body>
    <section class="login">
        <div class="container grid">
            <div class="admin-panel card">
                <h2>Login to Your Account</h2>
                <form action="login.php" method="POST">
                    <input type="hidden" name="CSRF" value="<?= $_SESSION['CSRF'] ?>">
                    <div class="form-control"> <label><h2>Email</h2></label> <input type="text" name="email" placeholder="Enter your email..."> </div>
                    <div class="form-control"> <label><h2>Password</h2></label> <input style="width: 50%" type="password" name="password" placeholder="Enter your password..."> </div>
                    <input class="btn" type="submit" value="Submit"></input>     
                </form>
                <a href="index.php"><button class='btn' type="button">Back to Homepage</button></a>
            </div>
        </div>
    </section>
</body>
</html>