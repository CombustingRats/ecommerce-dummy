<?php
    // TODO: make it so that can add item to cart by clicking on the icon
    include_once 'includes/dbh.php';

    // Select all the categories in the categories table (for category list population)
    $result = $conn->query("SELECT * FROM categories");

    // Join the databases of products and categories where the catids are equal. Because name appears twice, rename c.name to cname
    // Select only the entry of the current product and save columns as variables (for use in the display)
    $stmt = $conn->prepare("SELECT p.pid, p.catid, p.name, p.price, p.description, p.image, c.name cname FROM 
                            products p JOIN categories c ON p.catid = c.catid WHERE p.pid = :pid");
    $stmt->bindValue(":pid", $_GET['id']);
    $stmt->execute();

    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

    $name = $row2['name'];
    $price = $row2['price'];
    $description = $row2['description'];
    $image = $row2['image'];
    $catid = $row2['catid'];
    $cname = $row2['cname'];
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
  <script src="js/script.js"></script>
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
            <li><a href="categorypage.php?id=<?= $catid ?>"><h1><?= htmlspecialchars($cname) ?></h1></a></li>
            <li><h1> > </h1></li>
            <li><a href="#"><h1> <?= $name ?> </h1></a></li>
        </ul>
      <ul>
        <li><h2>Sort By</h2></li>
        <li><i class="fas fa-sort"></i></li>
      </ul>
      
    </div>
  </div>

  <!-- Category & Single Product Details -->
  <section class="products my-2">
    <div class="container grid">
      <div class="categories-list card bg-light">
        <nav class="p-2">
          <ul>
            <!-- Populate the Categories List -->
            <?php foreach ($result as $row) : ?>

              <li><a href="categorypage.php?id=<?= $row['catid'] ?> "> <?= htmlspecialchars($row['name'])?> </a></li>
            
            <?php endforeach; ?>
          </ul>
        </nav>
      </div>

      <div class="product-card card">
          <!-- Populate the Product Details -->
          <div class="product-details grid">
              <div class="product-display p-2">
                <img src="images/<?= $image ?>" alt="">
              </div>
              <div class="product-description">
                  <h2 class="m my-1"><?= htmlspecialchars($name) ?></h2>
                  <h3 class="my-1">Price: <?= $price ?></h3>
                  <h3>Product Highlights: </h3>
                  <p><?= htmlspecialchars($description)?>
                    </p>
                    <button class="btn add-cart my-3" data-product-id="<?=$_GET['id']?>"> <i class="fas fa-cart-plus fa-2x"></i> </button>
                    
              </div>
          </div>
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