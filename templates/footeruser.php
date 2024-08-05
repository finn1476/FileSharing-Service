<div class="divfooter">
    <h1><a class="footerlinks" href="../FAQ.php">FAQ</a></h1>
    <h1><a class="footerlinks" href="../impressum.php">Imprint</a></h1>
    <h1><a class="footerlinks" href="../datenschutz.php">Privacy Notice</a></h1>
    <h1><a class="footerlinks" href="../index.php">Home</a></h1>
    <?php
    session_start();
    if (isset($_SESSION['username'])) {
        echo '<h1><a class="footerlinks" href="index.php">Welcome, ' . htmlspecialchars($_SESSION['username']) . '!</a></h1>';
    } else {
        echo '<h1><a class="footerlinks" href="login.php">Login</a></h1>';
    }
    ?>
    <h1><a class="footerlinks" href="../abuse.php">Abuse</a></h1>
    <h1><a class="footerlinks" href="../terms.php">Terms of Service </a></h1>
</div>

