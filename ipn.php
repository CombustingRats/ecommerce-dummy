<?php
require('PaypalIPN.php');
include_once 'includes/dbh.php';

$ipn = new PaypalIPN();

// Use the sandbox endpoint during testing.
$ipn->useSandbox();
$verified = $ipn->verifyIPN();
if ($verified) {/*

    $handle = fopen('test.txt', 'w');

    foreach($_POST as $key => $value) {
        fwrite($handle, "$key => $value /r /n");
    }
} else {

    $handle = fopen('test.txt', 'w');
    fwrite($handle, "can't verify");

}*/

    /*
     * Process IPN
     * A list of variables is available here:
     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
     */

    // Get the IPN hash back from paypal and compare it to our hash 
    
    $currency = $_POST['mc_currency'];
    $email = $_POST['business'];
    $oid = $_POST['invoice'];
    $total = $_POST['mc_gross'];

    $count = $_POST['num_cart_items'];
    
    $source = array();

    for ($i = 1; $i <= $count; $i++) {
        $id = $_POST['item_number' . $i];
        $quantity = $_POST['quantity' . $i];

        $key = 'item_id_' . $i;
        $key2 = 'quantity_' . $i;

        $stmt=$conn->prepare("SELECT * from products WHERE pid = :pid");
        $stmt->bindValue(':pid', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $price = $row['price'];

        // Add the product id, product quantity, and product price to the digest
        $source[$key] = $id;
        $source[$key2] = $quantity;
        $source['price_' . $i] = $price;
    }
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE oid = :oid");
    $stmt->bindValue(':oid', $oid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $salt = $row['salt'];

    $source['currency'] = 'HKD';
    $source['email'] = 'sb-sleqe5957775@business.example.com';
    $source['salt'] = $salt;
    $source['total'] = $total;

    $json = json_encode($source);
    $hashvalue = hash('sha256', $json);
    $hash = $_POST['custom'];

    /*
    $stmt = $conn->prepare("UPDATE orders SET paymentstatus = :paymentstatus WHERE oid = :oid");
    $stmt->bindValue(':paymentstatus', 'abcd');
    $stmt->bindValue(':oid', $oid);
    $stmt->execute();
    */
    
    if ($hash === $hashvalue){
        $txn_id = $_POST['txn_id']; 
        $stmt = $conn->prepare("UPDATE cartitems, orders SET paymentstatus = :paymentstatus WHERE oid = :oid");
        $stmt->bindValue(':cartitems', $json);
        $stmt->bindValue(':paymentstatus', $txn_id);
        $stmt->bindValue(':oid', $oid);
        $stmt->execute();

        header("HTTP/1.1 200 OK");
    } else {
        error_log("After Checkout:\n" . json_encode($source));
        error_log(print_r($_POST, true));
    }

    // Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
    
}

?>