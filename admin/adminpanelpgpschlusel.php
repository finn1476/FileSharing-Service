<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targetDir = "../pgppublickey/"; // Verzeichnis, in dem die Dateien gespeichert werden sollen
    $allowedExtensions = ["asc"]; // Erlaubte Dateiendungen
    $maxFileSize = 2 * 1024 * 1024; // Maximale Dateigröße (2 MB)

    $uploadedFile = $_FILES["userfile"];
    $originalName = $uploadedFile["name"];
    $tmpName = $uploadedFile["tmp_name"];
    $fileSize = $uploadedFile["size"];
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

  

    // Umbenennen der Datei in "key.asc"
    $targetPath = $targetDir . "key.asc";
function isExpiryDateNear($expiryDate) {
    $today = date("Y-m-d");
    $expiryTimestamp = strtotime($expiryDate);
    $tenDaysLater = strtotime("+10 days", strtotime($today));
    return $expiryTimestamp <= $tenDaysLater;
}
    // Überschreiben der vorhandenen Datei
    if (move_uploaded_file($tmpName, $targetPath)) {
        echo "Datei erfolgreich hochgeladen und als key.asc gespeichert.";

        // Ablaufdatum aus dem Eingabefeld auslesen
        $expiryDate = $_POST["expiry_date"]; // Beispiel: "2024-03-31"

        // Ablaufdatum in CSV-Datei speichern
        $csvRow = "key.asc,$expiryDate\n"; // CSV-Zeile

        // CSV-Datei öffnen und Zeile hinzufügen
        $csvFile = fopen("../Speicher/file_expiry.csv", "w");
        fwrite($csvFile, $csvRow);
        fclose($csvFile);
    } else {
        echo "Fehler beim Hochladen der Datei.";
    }
}
		
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dateiupload</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<main>
    <h1>Admin PGP Schlüssel Hinzufügen</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="userfile">
        <input type="date" name="expiry_date"><br><br> <!-- Eingabefeld für das Ablaufdatum -->
        <center><input type="submit" value="Hochladen"></center>
    </form>
</main>
<style>
main{
	  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
</style>
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
