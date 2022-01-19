<?php
    //TODO: Unique names for the files once they are uploaded, Check file type before uploading
    //echo dirname(__DIR__) . '/images/';
    include_once '../includes/dbh.php';
    session_start();
    include_once '../includes/authorization.php';

    if(empty($_SESSION['CSRF'])){
        $_SESSION['CSRF'] = bin2hex(random_bytes(32));
    }

    // Fetch product details
    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE pid = :pid");
        $stmt->bindValue(":pid", $_GET['id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['CSRF'], $_POST['CSRF'])) {
        unset($_SESSION['CSRF']);
        if (isset($_GET['id'])) {
            // EDIT PRODUCT
            $name = $_POST['name'];
            $category = $_POST['category'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            // Check that all the fields of the form are filled
            if( !empty($name) &&
                intval($category) > 0 && //pretty sketchy
                is_numeric($price) &&
                !empty($description)) {
                
                // Check if a file has been uploaded via HTTP POST, If so set the filename and target directory
                if (is_uploaded_file($_FILES['image']['tmp_name'])
                    && $_FILES['image']['error'] == 0)
                    //&& mime_content_type($_FILES['image']['tmp_name'] == "image/jpeg")) 
                    {
                    $uploaddir = dirname(__DIR__) . '/images/';
                    $filename = basename($_FILES['image']['name']);
                    $uploadfile = $uploaddir . $filename;
                    // echo $uploadfile;

                    // Move the file from the temporary location into the images folder
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                        chmod($uploadfile, 0755);

                    } else {
                        echo "Error in Uploading File";
                    }

                    // UPDATE the database to reflect the new information
                    $stmt = $conn->prepare("UPDATE products SET name = :name, catid = :catid, price = :price, description = :description, image = :image  
                    WHERE pid = :pid ");
                    $stmt -> bindValue(":pid", $_GET['id']);
                    $stmt -> bindValue(":name", $name);
                    $stmt -> bindValue(":catid", $category);
                    $stmt -> bindValue(":price", $price);
                    $stmt -> bindValue(":description", $description);
                    $stmt -> bindValue(":image", $filename);
                    $stmt -> execute();

                
                } else {
                    // No Image is uploaded, Update the database to reflect other information 
                    $stmt = $conn->prepare("UPDATE products SET name = :name, catid = :catid, price = :price, description = :description
                    WHERE pid = :pid ");
                    $stmt -> bindValue(":pid", $_GET['id']);
                    $stmt -> bindValue(":name", $name);
                    $stmt -> bindValue(":catid", $category);
                    $stmt -> bindValue(":price", $price);
                    $stmt -> bindValue(":description", $description);
                    $stmt -> execute();
                }
                header("Location: product.php");
            }

        } else {
            // ADD PRODUCT
            $name = $_POST['name'];
            $category = $_POST['category'];
            $price = $_POST['price'];
            $description = $_POST['description'];

            // Check that all the fields are filled. ??? 
            if( !empty($name) &&
                intval($category) > 0 &&
                is_numeric($price) &&
                !empty($description)) {
            
                if (is_uploaded_file($_FILES['image']['tmp_name'])) {

                    echo "AAAAAAAAAA";
                    $uploaddir = '../images/';
                    $filename = basename($_FILES['image']['name']);
                    $uploadfile = $uploaddir . $filename;
        
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                        chmod($uploadfile, 0755);
                    } else {
                        echo "File could not be uploaded";
                    }
                    
                    $stmt = $conn->prepare("INSERT INTO products (name, catid, price, description, image) VALUES (:name, :catid, :price, :description, :image)");
                    $stmt -> bindValue(":name", $name);
                    $stmt -> bindValue(":catid", $category);
                    $stmt -> bindValue(":price", $price);
                    $stmt -> bindValue(":description", $description);
                    $stmt -> bindValue(":image", $filename);
                    $stmt -> execute();

                } else {

                    $stmt = $conn->prepare("INSERT INTO products (name, catid, price, description) VALUES (:name, :catid, :price, :description)");
                    $stmt -> bindValue(":name", $name);
                    $stmt -> bindValue(":catid", $category);
                    $stmt -> bindValue(":price", $price);
                    $stmt -> bindValue(":description", $description);
                    $stmt -> execute();

                }
                
                header("Location: product.php");
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
                <h2>Edit Product</h2>
            <?php else :?>
                <h2>Add Product</h2>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="CSRF" value="<?= $_SESSION['CSRF'] ?>">

                <div class="form-control">
                    <label><h2> Product Name: </h2></label>
                    <?php if (isset($row)) : ?>
                        <input type="text" name="name" placeholder="Product Name" value="<?= $row['name']?>" required>
                    <?php else : ?>
                        <input type="text" name="name" placeholder="Product Name" required>
                    <?php endif; ?>                       
                </div>

                <div class="form-control">
                    <label><h2>Category:</h2></label>
                    <select id="category" name="category" required>
                    <?php
                        $result2 = $conn->query("SELECT * FROM categories");
                        foreach ($result2 as $row2) : ?>
                            <?php //if the catid of the product matches that of the category, add the selected tag, else add nothing?>
                            <option value="<?= $row2['catid'] ?>" <?= (isset($row['catid']) && $row['catid'] == $row2['catid'])?'selected':'' ?>><?= $row2['name'] ?></option>
                        <?php endforeach;
                    ?>
                    </select>
                </div>

                <div class="form-control">
                    <label><h2> Product Price: </h2></label>
                    <?php if (isset($row)) : ?>
                        <input type="number" name="price" placeholder="Price" value="<?= $row['price']?>" min="0" required>  
                    <?php else : ?>
                        <input type="number" name="price" placeholder="Price" min="0" required>
                    <?php endif; ?> 
                    
                </div>

                <div class="form-control">
                    <label><h2> Product Description: </h2></label>
                    
                    <?php if (isset($row)) : ?>
                        <input type="text" name="description" placeholder="Description" value="<?= $row['description']?>" required>
                    <?php else : ?>
                        <input type="text" name="description" placeholder="Description" required>
                    <?php endif; ?> 
                </div>

                <?php if(!empty($row['image'])) : ?>
                <img src="../images/<?= $row['image'] ?>" style="width: 200px"/>
                <?php endif; ?>
                <div class="form-control">
                    <label><h2>Product Image</h2></label><input type="file" name="image">
                </div>

                <button class="btn" type="submit" name="submit">Publish</button>
        
            </form>
            
        </div>      
    </div>
</body>
</html>

