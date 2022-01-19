<?php
    // TODO: make it so that if the cart is empty, the user is sent back to previous page

    include_once 'includes/dbh.php';
    
    // Use the quantity input in the checkout form to retrieve the shopping cart
    // the quantities are posted as an associative array of key value pairs (see js) with the KEYS as pid and VALUES as quantity
    $quantities = $_POST['quantity'];
    
    // Fetch the corresponding record of all parameters for each PID in the shopping cart.
    // Store them in an associative array $products labeled by the PID of each item
    $products = array();
    foreach ($quantities as $id => $quantity) {
        $stmt = $conn->prepare('SELECT * FROM products WHERE pid = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $products[$id] = $stmt->fetch(PDO::FETCH_ASSOC);
        $products[$id]['quantity'] = $quantity;
    }
    $i = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/utilities.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="container">
        <div class="admin-panel card p-2">
            <table class="text-center" style="width: 100%">
                <thead>
                    <tr class="smmd">
                        <th style = "border-bottom: 2px solid black"><b>ID</b></th>
                        <th style = "border-bottom: 2px solid black"><b>Product</b></th>
                        <th style = "border-bottom: 2px solid black"><b>Quantity</b></th>
                        <th style = "border-bottom: 2px solid black"><b>Price</b></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- print out the shopping cart from the products array-->
                    <?php foreach ($products as $id => $product) : ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $product['name'] ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td>$<?= $product['price'] ?> x <?= $product['quantity'] ?> = <?= $product['price'] * $product['quantity'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php
                    // Display the Cart Total
                    $total = 0;
                    foreach ($products as $id => $product) {
                        $batchPrice = $product['quantity'] * $product['price'];
                        $total += $batchPrice;
                    }
                    $class = "class='smmd'";
                    $class2 = "style = 'border-top: 2px solid black'";
                    echo
                    "<tr $class>
                        <td></td>
                        <td></td>
                        <td $class2>Total Price: </td>
                        <td $class2>$$total</td>
                    </tr>"
                    ?>
                </tbody>
            </table>
            <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="POST" onsubmit="return beforeCheckout();">
                <input type="hidden" name="charset" value="utf-8">
                <input type="hidden" name="currency_code" value="HKD">
                <input type="hidden" id="invoice" name="invoice">
                <input type="hidden" id="custom" name="custom">
                <input type="hidden" name="cmd" value="_cart">
                <input type="hidden" name="upload" value="1">
                <input type="hidden" name="business" value="sb-sleqe5957775@business.example.com">

                <?php /*
                <input type="hidden" name="amount" value="<?=number_format($total, 2, '.', '')?>">
                */ ?>
                

                <?php foreach ($products as $id => $product) : ?>
                <?php $i++ ?>
                <input type="hidden" name="item_name_<?= $i ?>" value="<?= $product['name'] ?>">
                <input type="hidden" name="item_number_<?= $i ?>" value="<?= $id ?>">

                <input type="hidden" name="amount_<?= $i ?>" value="<?= number_format($product['price'], 2, '.', '') ?>">
                
                <input type="hidden" name="quantity_<?= $i ?>" value="<?= $product['quantity']?>">
                <input type="hidden" name="item_id_<?= $i ?>" value="<?=$product['pid']?>"> 
                <?php endforeach; ?>
                <!--
                <input type="hidden" name="notify_url" value="http://ec2-18-167-70-222.ap-east-1.compute.amazonaws.com/ipn.php"> -->
                <div style="text-align: right; margin-right:6%;">
                    <!--<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"> --> 
                    <input class="btn" type="submit" value="Checkout">
                </div>
            </form>
        </div>
    </div>
</body>
</html>



