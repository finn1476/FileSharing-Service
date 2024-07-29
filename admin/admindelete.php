<?php
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <main>
        <div class="awasr">
            <div><h2>Anonymer File Upload</h2><br></div>
            <div class="maske"><img src="../bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
            <h1>Admin Panel</h1>

    <form action="admindeletedesision.php" method="post">
                <label for="filenameInput">Enter Filename:</label>
                <input type="text" id="filenameInput" name="filename" required>
                <button type="submit" name="preview">Generate Preview</button>
            </form>
	<form  action="listdeactivefiles.php" method="POST" name="">
			<button class="buttona" type="submit" name="preview">Deaktivierte Datein Anzeigen</button>
	</form>
	<form  action="auswertungreport.php" method="POST" name="">
			<button class="buttona" type="submit" name="preview">Reports</button>
	</form>
            <a class="buttona" href="adminpanel.php">ADMIN</a></p>
        </div>
    </main>

  <footer class="footera">
<div>
<h1 class="right"><a class="bauttona" href="adminpanel5.php">Statistiken</a></h1>
</div>
<div>
<h1 class="right"><a class="bauttona" href="adminpanel4.php">Datei-Typen</a></h1>
</div>
<div>
<h1 class="right"><a class="bauttona" href="adminpanel3.php">Benutzer-Verwaltung</a></h1>
</div>
<div>
<h1 class="right"><a class="bauttona" href="adminpanel2.php">Upload-Grenze</a></h1>
</div>
<div>
<h1><a class="bauttona" href="admindelete.php">Löschen</a></h1>
</div>

</footer>
</body>
</html>
