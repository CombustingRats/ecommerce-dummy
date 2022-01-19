<?php
    include_once '../includes/dbh.php';
    session_start();
    include_once '../includes/authorization.php';

    // select all the categories from the categories table
    $result = $conn->query("SELECT * FROM categories");
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
            <a href="categoryedit.php"><button class="btn" type="submit" name="submit">Add</button></a>
            <a href="logout.php" style="float:right"><button class="btn" type="submit" name="submit">Logout</button></a>
            <table class="text-center" style="width: 100%">
                <tr class="smmd">
                    <th style = "border-bottom: 2px solid black">Category ID</th>
                    <th style = "border-bottom: 2px solid black">Name</th>
                </tr>
                <?php foreach ($result as $row) : //display all the categories in the table?>
                    <tr>
                        <td><?= $row['catid'] ?></td>
                        <?php
                        // Redirect to the correct editing page with the query containing number of catid
                        ?>
                        <td><a href="categoryedit.php?id=<?=$row['catid']?>"><?= htmlspecialchars($row['name']) ?></a></td>
                    </tr>
                <?php endforeach;?>
            </table>
            <div style="text-align: right;">
                <a href="../index.php"><button class="btn" type="submit" name="submit">Homepage</button></a>
                <a href="vieworders.php"><button class="btn" type="submit" name="submit">View Orders</button></a>
                <a href="product.php"><button class="btn" type="submit" name="submit">Product Panel</button></a>
                <a href="changepassword.php"><button class="btn" type="submit" name="submit">Change Password</button></a>
            </div>

    </div>
    
</body>
</html>

