<!DOCTYPE>
<html>
<head>
	<title>404 - Page not found</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<?php

$currentDomain = $_SERVER['HTTP_HOST'];	
	
?>
<body>
<div class="awasr">
 <div class="maske"><img src="../bilder\vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
    <h2>404 - Page not found</h2>
    <p>The page you requested could not be found.</p>
    <a class="buttona" href="https://<?php echo"$currentDomain" ?>">Back to Home</a>
</div>
<footer class="footera">
<div>
<h1 class="right"><a class="bauttona" href="impressum.php">Impressum</a></h1>
</div>
<div>
<h1><a class="bauttona" href="datenschutz.php">Datenschutzerklärung</a></h1>
</div>
</footer>
</body>
</html>
