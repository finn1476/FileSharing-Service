<?php
// Überprüfen, ob das Formular gesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eingegebene E-Mail-Adresse erhalten
    $email = $_POST["email"];

    // Pfad zur CSV-Datei
    $csvFilePath = '../Speicher/email.csv';

    // CSV-Datei öffnen oder erstellen
    $csvFile = fopen($csvFilePath, "w");

    // E-Mail-Adresse in die CSV-Datei schreiben
    fwrite($csvFile, $email);

    // CSV-Datei schließen
    fclose($csvFile);

    // Erfolgsmeldung anzeigen
    echo "<script>
        alert('Email-Adresse erfolgreich aktualisiert.');
        window.location.href = 'index.php';
        </script>";
} else {
    // Falls das Formular nicht gesendet wurde, Fehlermeldung anzeigen
    echo "<script>
        alert('Error: Form was not submitted.');
        window.location.href = 'index.php';
        </script>";
}
?>
