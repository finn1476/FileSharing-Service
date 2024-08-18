<div class="divfooter">
    <h1><a class="footerlinks" href="FAQ.php">FAQ</a></h1>
    <h1><a class="footerlinks" href="impressum.php">Imprint</a></h1>
    <h1><a class="footerlinks" href="datenschutz.php">Privacy Notice</a></h1>
    <h1><a class="footerlinks" href="index.php">Home</a></h1>
    <?php
    
    if (isset($_SESSION['username'])) {
        echo '<h1><a class="footerlinks" href="User/index.php">Welcome, ' . htmlspecialchars($_SESSION['username']) . '!</a></h1>';
    } else {
        echo '<h1><a class="footerlinks" href="User/login.php">Login</a></h1>';
    }
    ?>
    <h1><a class="footerlinks" href="abuse.php">Abuse</a></h1>
    <h1><a class="footerlinks" href="terms.php">Terms of Service </a></h1>
	<h1><a class="footerlinks" href="pricing.php">Pricing </a></h1>
</div>
