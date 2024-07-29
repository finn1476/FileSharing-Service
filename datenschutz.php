<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Datenschutzerklärung</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        main {
            text-align: center;
        }

        .awasr {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 600px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
        }

        .impressum {
            text-align: left;
            margin-top: 30px;
        }
    </style>
<main>
    <div class="awasr">
        <div>
            <h2>Datenschutzerklärung</h2>
        </div>
        <div class="maske">
            <img src="bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/>
        </div>
        <div class="impressum">
            <?php
                $csv = "Speicher/datenschutzerklaerung.csv"; // Pfad zur CSV-Datei

                // CSV-Datei einlesen
                $csvFile = fopen($csv, 'r');

                // Daten aus CSV lesen und anzeigen
                $skipHeaders = false; // Überschriften überspringen
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    // Überschriften überspringen
                    if ($skipHeaders) {
                        $skipHeaders = false;
                        continue;
                    }
                    // Kontaktdaten Überschrift
                    echo "<p><strong>Kontaktdaten</strong><br>";
                    // Daten anzeigen
                    echo $line[0] . "</p>";

                    // Welche Daten speichern wir? Überschrift
                    echo "<p><strong>Welche Daten Speichern wir?</strong><br>";
                    // Daten anzeigen
                    echo $line[1] . "</p>";

                    // What data do we store? Überschrift
                    echo "<p><strong>What data do we store?</strong><br>";
                    // Daten anzeigen
                    echo $line[2] . "</p>";
                }

                // CSV-Datei schließen
                fclose($csvFile);
            ?>
        </div>
        <footer>
            <?php include("templates/footer.php"); ?>
        </footer>
    </div>
</main>
</body>
</html>
