<?php
    session_start();
    include_once '../includes/dbh.php';
    include_once '../includes/authorization.php';

    // CSRF Token
    if(empty($_SESSION['CSRF'])){
        $_SESSION['CSRF'] = bin2hex(random_bytes(32));
    }

    // Retrieve the details of the current category into $row
    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE catid = :catid");
        $stmt->bindValue(":catid", $_GET['id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ** Handles the POST request made by the form
    // Check if the request made in the form is POST and confirms that the session is valid
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['CSRF'], $_POST['CSRF'])) {
        unset($_SESSION['CSRF']);

        // ** Edit a category
        // Checks if the POST request has an id, which means its an edit request
        if (isset($_GET['id'])) {

            // Checks to make sure the name field is filled before processing the edit
            $name = $_POST['name'];
            if(!empty($name)) {

                // Update the corresponding category name in the database and redirect back to the main category-admin
                $stmt = $conn->prepare("UPDATE categories SET name = :name WHERE catid = :catid");
                $stmt->bindValue(":catid", $_GET['id']);
                $stmt->bindValue(":name", $name);
                $stmt->execute();
                header("Location: category.php");
            }
        } else {

            // If its an add request, the id will be blank
            $name = $_POST['name'];
            if(!empty($name)) {
            // Insert instead of Update the database and redirect back out
                $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
                $stmt->bindValue(":name", $name);
                $stmt->execute();
                header("Location: category.php");
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/utilities.css">
</head>
<body>
    <div class="container">
            
        <div class="admin-panel card p-2">
            <a href="logout.php" style="float:right"><button class="btn" type="submit" name="submit">Logout</button></a>
            <?php if (isset($_GET['id'])) :?>
                <h2>Edit Category</h2>
            <?php else :?>
                <h2>Add Category</h2>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="CSRF" value="<?= $_SESSION['CSRF'] ?>">
                <div class="form-control">
                    <label><h2> Category Name: </h2></label>
                    <?php
                    // if the row variable exists, it is an edit request, we insert the existing catname into the input box
                    ?>
                    <?php if (isset($row)) : ?>
                        <input type="text" name="name" placeholder="Category Name" value="<?= htmlspecialchars($row['name']); ?>" required>
                    <?php else : ?>
                        <input type="text" name="name" placeholder="Category Name" required>
                    <?php endif; ?>
                </div>

                <button class="btn" type="submit" name="submit">Publish</button>
        
            </form>

    </div>
    
</body>
</html>

