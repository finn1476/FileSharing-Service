 
<?php
if (isset($_POST['status'])) {
    $status = $_POST['status'];

    // Pfad zur CSV-Datei
    $csvFile = '../Speicher/userportal.csv';

    // Öffne die CSV-Datei zum Schreiben
    $file = fopen($csvFile, 'w');

    // Schreibe den neuen Status in die CSV-Datei
    fputcsv($file, [$status]);

    // Schließe die Datei
    fclose($file);

    echo 'Status erfolgreich gespeichert';
} else {
    echo 'Kein Status angegeben';
}
?>
