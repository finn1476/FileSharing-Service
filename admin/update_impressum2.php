 
<?php
// Überprüfen, ob das Formular gesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formulardaten erhalten
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $disclaimer = $_POST["disclaimer"];

    // Neuen Eintrag für das Impressum in CSV-Datei schreiben
    $csvFile = fopen('../Speicher/impressum.csv', 'w');
    fputcsv($csvFile, array($name, $email, $phone, $disclaimer));
    fclose($csvFile);

    // Weiterleitung zur Bestätigungsseite oder zurück zum Admin-Panel
    header("Location: update_impressum.php");
    exit();
}
?>
