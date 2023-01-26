<?php

use Database\DatabaseTable;

if (!isset($_SESSION) && $_SESSION['password_verified']) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title><?= $title ?? 'Jo\'s Job - Home' ?></title>
</head>

<body>
<header>
    <section>
        <aside>
            <h3>Office Hours:</h3>
            <p>Mon-Fri: 09:00-17:30</p>
            <p>Sat: 09:00-17:00</p>
            <p>Sun: Closed</p>
        </aside>
        <h1>Jo's Jobs</h1>
    </section>
    <!DOCTYPE html>

</header>
<?php
if (isset($_SESSION['loggedin']) && $_SESSION['password_verified'] && $_SESSION['userDetails']['userType'] == 'admin') {
    echo '<div class="log-out"><p>Hello, ' . $_SESSION['userDetails']['fullName'] . ' .<a href="../admin/adminIndex">Admin-Home</a> <a href="logOut">Log-out</a></div></p>';
} else if (isset($_SESSION['loggedin']) && $_SESSION['password_verified'] && $_SESSION['userDetails']['userType'] == 'client') {
    echo '<div class="log-out"><p>Hello, ' . $_SESSION['userDetails']['fullName'] . ' .<a href="../admin/clientIndex">Client-Home</a> <a href="logOut">Log-out</a></div></p>';
} else {
    echo '<div class="admin-login">
                   <button><a href="../admin/adminLogin" style="color: white">Admin/Client Login</a></button>
              </div>';
}
?>
<nav>
    <ul>
        <li><a href="../home">Home</a></li>
        <li>Jobs
            <ul>
                <?php
                $categoryTable = new DatabaseTable('category', 'id', 'job');
                $categories = $categoryTable->findAll();
                foreach ($categories as $category) {
                    echo '<li><a href="../categories?id=' . $category['id'] . '">' . $category['name'] . '</a></li>';
                }
                ?>
            </ul>
        </li>
        <li><a href="../aboutUs">About Us</a></li>
        <li><a href="../contact">Contact</a></li>
        <li><a href="../faqs">FAQs</a></li>
    </ul>

</nav>
<img src="/images/randombanner.php"/>
<?= $output ?? '' ?>
<?php require 'footer.html.php' ?>
</body>
</html>