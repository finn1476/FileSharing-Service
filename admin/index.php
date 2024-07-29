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
    <script>
        function hideWarning() {
            document.getElementById('warning_message').style.display = 'none';
        }
    </script>
</head>
<body>
<main>
<?php
    // Read expiry timestamp from file_expiry.csv
    $csvFile = '../Speicher/file_expiry.csv';
    $csvContent = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $expiryTimestamp = strtotime(explode(',', $csvContent[0])[1]); // Extracting the expiry timestamp from CSV line

    // Current timestamp
    $now = time();

    // Calculate remaining days
    $remainingDays = ceil(($expiryTimestamp - $now) / (60 * 60 * 24));

    // Check if warning should be displayed
    if ($remainingDays <= 10) {
        echo '<div id="warning_message" class="warning">';
        echo '<h3>Warning!</h3>';
        echo "<p>Publix PGP Key will expire in $remainingDays days. Do you want to Ignore it or Fix it?</p>";
        echo '<button class="red" onclick="hideWarning()">Ignore</button>';
        echo '<button class="green" onclick="window.location.href = \'adminpanelpgpschlusel.php\'">Fix</button>';
        echo '</div>';
    }
?>
<div class="awasr">
    <div ><h2>Anonymer File Upload</h2><br></div>
    <div class="maske"><img src="../bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
    <h1>Admin Panel</h1>
    <form action="admin.php" method="post" enctype="multipart/form-data">
        <label for="filename">Dateiname:</label><br>
        <input type="text" id="filename" name="filename"><br>
        <input type="submit" name="disable" value="Download deaktivieren">
        <input type="submit" name="enable" value="Download aktivieren">
        <input type="submit" name="delete" value="Datei löschen">
    </form><br><br><p>
    <form action="admin.php" method="post" enctype="multipart/form-data">
        <label for="csvFile">CSV Block Datei hochladen:</label><br>
        <input type="file" name="csvFile" id="csvFile" accept=".csv" >
        <input type="submit" name="updateFromCSV" value="Aktualisieren von CSV">
    </form></p><p>
    <a class="buttona" href="../Speicher/hashes.csv">Download Block CSV Datei</a>
    <a class="buttona" href="../index.php">HOME</a>
</p>
<p><a class="buttona" href="adminpanel2.php">Nächste Seite</a></p>
<p><a class="buttona" href="update_impressum.php">Edit Imprint</a>
<a class="buttona" href="update_datenschutz.php">Edit Privaciy Notice</a>
<a class="buttona" href="email.php">Abuse Email</a>
<a class="buttona" href="monero.php">Donation</a>
<a class="buttona" href="Deletefiles.php">Delete Old Files</a>
<a class="buttona" href="userportal.php">Userportal</a>
</p>
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
