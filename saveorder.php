<?php
    include_once 'includes/dbh.php';

    $_POST['item_id'];
    $_POST['quantity'];
    $cartitems =  array();
    $source = array();
    $total = 0;
    $userid = NULL;

    if (isset($_COOKIE['auth'])) {
        // Check if the user is signed in. If so, return the userid
        $stmt = $conn->prepare("SELECT * FROM `auth` WHERE token = :token");
        $stmt->bindValue(":token", $_COOKIE['auth']);
        $stmt->execute();

        if($rowAuth = $stmt->fetch(PDO::FETCH_ASSOC)){
            $userid = $rowAuth['userid']; 
        }
    }
    // Computing cart items component in $cartitems for storage in the database
    $count = count($_POST['item_id']);
    for ($i = 0; $i < $count; $i++) {
        // Retrieve the item ID and quantity of each item which looks like array(item_id[1] => 18, item_id[2] => 1) etc...
        $id = $_POST['item_id'][$i];
        $quantity = $_POST['quantity'][$i];

        $key = 'item_id_' . strval($i+1);
        $key2 = 'quantity_' . strval($i+1);

        $cartitems[$key] =  $id;
        $cartitems[$key2] = $quantity;

        // Calculating cart total component of digest
        // Get the price of the product from the database
        $stmt=$conn->prepare("SELECT * from products WHERE pid = :pid");
        $stmt->bindValue(':pid', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $price = $row['price'];
        // Multiply it by the quantity and add it to total
        $total += $price * $quantity;

        // Add the product id, product quantity, and product price to the digest
        $source[$key] = $id;
        $source[$key2] = $quantity;
        $source['price_' . strval($i+1)] = $price;
    }
    $cartitemsStr = json_encode($cartitems);

    // insert cartitems, total and userid into the database
    $salt = bin2hex(random_bytes(2));
    $stmt=$conn->prepare("INSERT into orders (uid, cartitems, total, salt) VALUES (:uid, :cartitems, :total, :salt)");
    $stmt->bindValue(':uid', $userid);
    $stmt->bindValue(':cartitems', $cartitemsStr);
    $stmt->bindValue(':total', $total);
    $stmt->bindValue(':salt', $salt);
    
    $stmt->execute();

    // append the rest of the requirements into the digest
    $source['currency'] = 'HKD';
    $source['email'] = 'sb-sleqe5957775@business.example.com';
    $source['salt'] = $salt;
    $source['total'] = number_format($total, 2, '.', '');

    // hash the digest and send the ID and hash back to the client
    $hashValue = hash('sha256', json_encode($source));
    //error_log("Before Checkout:\n" . json_encode($source));
    $lastInsertId = $conn->lastInsertId();
    
    echo json_encode(array('lastInsertId' => $lastInsertId, 'hashValue' => $hashValue));
?>