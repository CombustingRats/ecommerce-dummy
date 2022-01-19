<?php
    include_once 'includes/dbh.php';
    // Fetch the table of categories and store them in $result1
    $result = $conn->query("SELECT * FROM categories");

    // fetch the table of products in our current category and store them in $result2
    $stmt = $conn->prepare("SELECT * FROM products WHERE catid = :catid");
    $stmt->bindValue(":catid", $_GET['id']);
    $stmt->execute();
    $result2 = $stmt->fetchAll();

    // fetch the details of the category that we are in and store it in $row3
    $stmt = $conn->prepare("SELECT * FROM categories WHERE catid = :catid");
    $stmt->bindValue(":catid", $_GET['id']);
    $stmt->execute();
    $row3 = $stmt->fetch(PDO::FETCH_ASSOC);

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dummy Shopping Website</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" 
    integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==
    " crossorigin="anonymous" />
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/utilities.css">
  <script src="js/script.js" async></script>
</head>
<body>

  <!-- Header -->
  <header>
    <!-- Navbar -->
    <?php
    include_once 'includes/navbar.php'
    ?>
  </header>

  <!-- Hierarchical Nav Display -->
  <div class="hierarchical-nav my-2">
    <div class="container flex">
        <ul>
            <li><a href="index.php"><h1>Home</h1></a></li>
            <li><h1> > </h1></li>
            <li><a href="#"><h1><?= htmlspecialchars($row3['name']) //get name of current category from $row3 ?></h1></a></li>
        </ul>
      <ul>
        <li><h2>Sort By</h2></li>
        <li><i class="fas fa-sort"></i></li>
      </ul>
      
    </div>
  </div>

  <!-- Product List -->
  <section class="products my-2">
    <div class="container grid">
      <div class="categories-list card bg-light">
        <nav class="p-2">
          <ul>
            <!-- Display the Categories List -->

            <?php foreach ($result as $row) : //display each category in $result?>
              <li><a href="categorypage.php?id=<?= $row['catid'] ?> "> <?= htmlspecialchars($row['name'])?> </a></li>
            <?php endforeach; ?>
          </ul>
        </nav>
      </div>
      <div class="card">
        <ul class="product-table">

          <!-- Display the Product Table -->
          <?php foreach ($result2 as $row2) : //display each each product from $result2 ?>
          <li>
            <!-- <a href="productpage.php?id=<?=$row2['pid']?>"><img src="images/<?= $row2['image']?>" alt=""></a> -->
            <a href="productpage.php?id=<?=$row2['pid']?>"><img src="includes/resizeimage.php?image=<?= $row2['image']?>" alt=""></a>
            <div class="flex">
                <div>
                    <a href="productpage.php?id=<?= $row2['pid'] ?>">
                        <h4> <?= htmlspecialchars($row2['name']) ?> </h4>
                    </a>
                        <p>$ <?= $row2['price'] ?> </p>
                </div>
                <button class="btn add-cart" data-product-id="<?=$row2['pid']?>"><i class="fas fa-cart-plus fa-2x"></i></button>
            </div>  
          </li>
          <?php endforeach; ?>  
           
        </ul>
      </div>
    </div>
  </section>

    <!-- Footer -->
    <footer class="footer bg-primary">
        <div class="container flex">
          <div class="div">
            <h1 class="logo">BuluLubu.</h1>
          </div>
          <div class="social">
            <a href="#"><i class="fab fa-facebook fa-2x"></i></a>
            <a href="#"><i class="fab fa-instagram fa-2x"></i></a>
          </div>
        </div>
      </footer>
    
</body>
</html>