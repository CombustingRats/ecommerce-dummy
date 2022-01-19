<?php
    include_once '../includes/dbh.php';
    session_start();
    include_once '../includes/authorization.php';
    $result = $conn->query("SELECT pid, p.name, price, image, c.name cname FROM `products` p, `categories` c WHERE p.catid = c.catid");
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
            <a href="productedit.php"><button class="btn" type="submit" name="submit">Add</button></a>
            <a href="logout.php" style="float:right"><button class="btn" type="submit" name="submit">Logout</button></a>
            <table class="text-center" style="width: 100%">
                <tr class="smmd">
                    <th style = "border-bottom: 2px solid black">Product ID</th>
                    <th style = "border-bottom: 2px solid black">Category</th>
                    <th style = "border-bottom: 2px solid black"> Name</th>
                    <th style = "border-bottom: 2px solid black"> Price</th>
                </tr>
                <?php foreach ($result as $row) : ?>
                    <tr>
                        <td><?= $row['pid'] ?></td>
                        <td><?= htmlspecialchars($row['cname'])?></td>
                        <td><a href="productedit.php?id=<?= $row['pid'] ?>"><?= htmlspecialchars($row['name'])?></a></td>
                        <td><?= $row['price']?></td>
                    </tr>
                <?php endforeach;?>
            </table>
            <div style="text-align: right;">
                <a href="vieworders.php"><button class="btn" type="submit" name="submit">View Orders</button></a>
                <a href="category.php"><button class="btn" type="submit" name="submit">Category Panel</button></a>
            </div>

    </div>
    
</body>
</html>