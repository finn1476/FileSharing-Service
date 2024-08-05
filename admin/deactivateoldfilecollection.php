<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <style>
        .warning {
            color: red;
        }
    </style>
 
</head>
<body>
<main>
<div class="awasr">
<h1>Option to deactivate File date Collection</h1>
<form  action="checked.php" method="POST" name="">
	<label>Deactivate File Date Collection</label>
	<input type="checkbox" name="check" value="1" <?php 
	$checked = 0;
		$datei = fopen("../Uploaded_Files/statusupload.csv","r");
		$checked = fgets($datei, 10);
		fclose($datei);
		
		
	if ($checked == 1) 
		{
			echo "checked='checked'";
		} ?> />
		<input type="submit" value="Senden" />
</form>
<?php
if ($checked == 1)
		{
			echo "Data collection enabled";
		}else{
            echo "Data collection disabled";
        }
		?>
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
