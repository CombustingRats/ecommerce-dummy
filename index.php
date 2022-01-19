<?php
    // TODO: Populate the big table with the first image of each category in the DB
    // TODO: How do we change the features depending on if the user is logged in?
    // TODO: Check if the user is an admin and let them access the admin panel page

    // Connect to the database with the db handler
    include_once 'includes/dbh.php';
    // Query the categories table and store them in the variables result and result 2
    $result = $conn->query("SELECT * FROM categories");
    $result2 = $conn->query("SELECT * FROM categories");

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
    <?php
    include_once 'includes/navbar.php'
    ?>
  </header>

  <!-- Display -->
  <section class="display bg-primary py-2">
    <div class="container grid">
      <div class="text-center">
        <h2 class="lg">Unbeatable Value</h2>
        <p class="lead my-2">Explore our latest selection of value products and limited time offers</p>
        <a href="category.html" class="btn">Shop now</a>
      </div>
      <img src="images/moai.jpg">
    </div>
  </section>
  
  <!-- Featured Products List -->
  <section class="featured-categories my-3 py-2">
    <h2 class="md text-center my-1">
      Featured Products 
    </h2>
    <div class="container flex">
      <div class="img-box">
        <h4>Cat</h4>
        <img src="images/cat.jpg" alt="">
      </div>
      <div class="img-box">
        <h4>Cheese</h4>
        <img src="images/cheese.jpeg" alt="">
      </div>
      <div class="img-box">
        <h4>Moai</h4>
        <img src="images/moai2.jpg" alt="">
      </div>
      <div class="img-box">
        <h4>Pika</h4>
        <img src="images/pika.jpeg" alt="">
      </div>
      <div class="img-box">
        <h4>Wine</h4>
        <img src="images/wine.jpeg" alt="">
      </div>
    </div>
  </section>

  <!-- Hierarchical Nav Display -->
  <div class="hierarchical-nav">
    <div class="container flex">
      <h1>Categories</h1>
      <ul>
        <li><h2>Sort By</h2></li>
        <li><i class="fas fa-sort"></i></li>
      </ul>
      
    </div>
  </div>

  <!-- Categories List -->
  <section class="products my-3">
    <div class="container grid">
      <div class="categories-list card bg-light">
        <nav class="p-2">
          <ul>
            
            <?php foreach ($result as $row) : //print out each row of categories?>
              <li><a href="categorypage.php?id=<?= $row['catid'] ?> "> <?= htmlspecialchars($row['name'])?> </a></li>
            <?php endforeach; ?>
              
          </ul>
        </nav>
      </div>
      <div class="card">
        <ul class="product-table">

          <?php foreach ($result2 as $row2) : ?>

          <?php
            $catid = $row2['catid'];
            $stmt = $conn->prepare("SELECT * FROM categories c JOIN products p ON c.catid = p.catid WHERE p.catid = :catid");
            $stmt->bindValue(':catid', $catid);
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
            $image = $row3['image'];
            //echo $image;
            
          ?>

          <li>
            <a href="categorypage.php?id=<?= $row2['catid'] ?> ">
              <img src="includes/resizeimage.php?image=<?= $image ?>">
              <h4><?= htmlspecialchars($row2['name']) ?></h4>
            </a>
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