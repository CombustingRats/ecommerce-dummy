<?php
    // TODO: Make this Look Nicer
    include_once 'includes/dbh.php';
    session_start();

    // Get the userid from the cookie
    $stmt = $conn->prepare("SELECT userid FROM auth WHERE token = :token");
    $stmt->bindValue(':token', $_COOKIE['auth']);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $userid = $row['userid'];

    $stmt = $conn->prepare("SELECT * FROM orders WHERE uid = :uid ORDER BY time DESC LIMIT 5");
    $stmt->bindValue(':uid', $userid);
    $stmt->execute();
    $result = $stmt->fetchAll();


    // $stmt = $conn->prepare("SELECT * FROM users u JOIN orders o ON u.userid = o.userid WHERE ")
    // $stmt = $conn->prepare("SELECT u.admin FROM auth a JOIN users u ON a.userid = u.userid WHERE a.token = :token");

    // Check if the user is an admin to display the admin panel in the navbar
    if(isset($_COOKIE['auth'])){
        $stmt = $conn->prepare("SELECT * FROM auth a JOIN users u ON a.userid = u.userid WHERE token = :token");
        $stmt->bindValue(':token', $_COOKIE['auth']);
        $stmt->execute();
        $rowAuth = $stmt->fetch(PDO::FETCH_ASSOC);
        $admin = intval($rowAuth['admin']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/utilities.css">
    <script src="js/script.js" async></script>
</head>
<body>
    <header>
        <?php 
        include_once 'includes/navbar.php';
        ?>
    </header>

    <section class="view-orders my-3 py-2">
        <div class="container">
            <h2 class="md text-center my-1">
            Recent Orders
            </h2>
            <?php foreach ($result as $row) : //display all order information?>
                <!--
                <tr>
                <td style = "border-bottom: 2px solid black"> -->
                <div class="order-card card">
                    <div class="order-display grid text-center">
                        <div class="order-details">
                            <?php
                                $decodedlist = json_decode($row['cartitems'], true);
                                $currentitem = 1;
                                $total = $row['total'];
                                $date = strval($row['time']);
                                $date_components = explode(":",  $date);

                                while(array_key_exists('item_id_' . $currentitem, $decodedlist)){
                                    $pid = $decodedlist['item_id_' . $currentitem];
                                    $quantity = $decodedlist['quantity_' . $currentitem];
                                    $stmt = $conn->prepare("SELECT * FROM products WHERE pid = :pid");
                                    $stmt->bindValue(':pid', $pid);
                                    $stmt->execute();
                                    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $productname =  $row2['name'];
                                    $productprice = $row2['price'];

                                    echo $productname . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $quantity . " x $" . $productprice . "</br>";
                                    $currentitem++;
                                }
                            ?>
                        </div>
                        <div class="order-price">
                            <div class="smmd"><?= 'Total: $' . $total ?></div>
                        </div>
                        <div class="order-date">
                            <?= 'Date of Purchase: &nbsp;&nbsp&nbsp' . $date_components[0] . ':' . $date_components[1]?>
                        </div>
                    </div>
                    
                </div>
                    
                <?php endforeach;?>
            </table>
        </div>
    </section>
</body>
</html>