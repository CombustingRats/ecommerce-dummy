<?php
    // TODO: what do we do if the user is not logged in?
    include_once '../includes/dbh.php';
    session_start();
    include_once '../includes/authorization.php';
    $result = $conn->query('SELECT * FROM orders');
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
            <table class="text-center" style="width: 100%">
                <tr class="smmd">
                    <th style = "border-bottom: 2px solid black">Order ID</th>
                    <th style = "border-bottom: 2px solid black">User ID</th>
                    <th style = "border-bottom: 2px solid black">Cart Items</th>
                    <th style = "border-bottom: 2px solid black">Total Price</th>
                    <th style = "border-bottom: 2px solid black">Time</th>
                    <th style = "border-bottom: 2px solid black">Payment Status</th>
                </tr>
                <?php foreach ($result as $row) : //display all order information?>
                    <tr>
                        <td style = "border-bottom: 2px solid black"><?= $row['oid'] ?></td>
                        <td style = "border-bottom: 2px solid black"><?= $row['uid'] ?></td>
                        <td style = "border-bottom: 2px solid black">
                        <?php 
                            $decodedlist = json_decode($row['cartitems'], true);
                            $currentitem = 0;
                            while(array_key_exists('item_id_' . $currentitem, $decodedlist)){
                                echo "Item ID: " . $decodedlist['item_id_' . $currentitem] . 
                                ", Quantity: " . $decodedlist['quantity_' . $currentitem] . "</br>";
                                $currentitem++;
                        }
                        // echo print_r($decodedlist);
                        ?>
                        </td>
                        <td style = "border-bottom: 2px solid black"><?= $row['total'] ?></td>
                        <td style = "border-bottom: 2px solid black"><?= $row['time'] ?></td>
                        <td style = "border-bottom: 2px solid black"><?= $row['paymentstatus'] ?></td>
                    </tr>
                <?php endforeach;?>
            </table>
            <div style="text-align: right;">
                <a href="category.php"><button class="btn" type="submit" name="submit">Category Panel</button></a>
                <a href="product.php"><button class="btn" type="submit" name="submit">Product Panel</button></a>
            </div>

    </div>
    
</body>
</html>