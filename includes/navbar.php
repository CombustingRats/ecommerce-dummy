<!-- Navbar -->
<div class="navbar">
    <div class="container flex">
    <h1 class="logo">BuluLubu.</h1>
    <ul>
        <li class="underscore"><a href="index.php">Home</a></li>
        <?php if(isset($_COOKIE['auth'])) : ?>
            <li class="underscore"><a href="viewmyorders.php">View Orders</a></li>
            <li class="underscore"><a href="admin/logout.php">Logout</a></li>
        <?php else : ?>
        <li class="underscore"><a href="login.php">Login</a></li>
        <?php endif; ?>

        <?php if(isset($admin) && $admin === 1) : ?>
        <li class="underscore"><a href="admin/category.php">Admin Panel</a></li>
        <?php else : ?>
        <?php endif; ?>
        <li>
        <form id="cart" class="cart" action="cart.php" method="POST">
            <a href="#"><i class="fas fa-shopping-cart fa-2x cart-button"></i></a>
            <ul class="cart-items">
            <!-- cart items go here (js) -->
            </ul>
        </form>
        </li>
    </ul>
    </div>
</div>