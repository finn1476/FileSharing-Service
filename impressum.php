<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Impressum</title>
    <link rel="stylesheet" href="style.css">
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
</head>
<body>
<main>
    <div class="awasr">
        <div>
            <h2>Impressum</h2>
        </div>
        <div class="maske">
            <img src="bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/>
        </div>
        <div class="impressum">
            <p><strong>Diensteanbieter:</strong><br>
                <?php
                $csv = "Speicher/impressum.csv"; // Korrekter Pfad zur CSV-Datei

                // CSV Datei einlesen
                $csvFile = fopen($csv, 'r');



                // Daten aus CSV lesen und anzeigen
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    echo $line[0] . '<br>';
                }

                // CSV Datei schließen
                fclose($csvFile);
                ?>
            </p>

            <p><strong>Kontaktmöglichkeiten:</strong><br>
                <?php
                // CSV Datei erneut öffnen, um Kontaktinformationen zu lesen
                $csvFile = fopen($csv, 'r');


                // Kontaktinformationen aus CSV lesen und anzeigen
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    echo 'E-Mail-Adresse: <a href="mailto:' . $line[1] . '">' . $line[1] . '</a><br>';
                    echo 'Telefon: ' . $line[2] . '<br>';
                }

                // CSV Datei schließen
                fclose($csvFile);
                ?>
            </p>

            <p><strong>Haftungs- und Schutzrechtshinweise:</strong><br>
                <?php
                // CSV Datei erneut öffnen, um Haftungsausschluss zu lesen
                $csvFile = fopen($csv, 'r');


                // Haftungsausschluss aus CSV lesen und anzeigen
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    echo $line[3] . '<br>';
                }

                // CSV Datei schließen
                fclose($csvFile);
                ?>
            </p>
        </div>
        <footer>
            <?php include("templates/footer.php"); ?>
        </footer>
    </div>
</main>
</body>
</html>
