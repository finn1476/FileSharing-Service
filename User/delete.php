<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Datei Löschen</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #333;
            color: #fff;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #444;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
        }

        .logout {
            text-align: center;
            margin-top: 20px;
        }

        .logout a {
            color: #f88;
            text-decoration: none;
        }

        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <h1>Datei Löschen</h1>
            <?php
            session_start();

            if (!isset($_SESSION['username'])) {
                header("Location: index.html");
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['filename'])) {
                $filename = $_GET['filename'];
                $uploadDir = '../Files/'; // Verzeichnis, in dem die Dateien gespeichert sind

                // Überprüfen, ob die Datei dem aktuellen Benutzer gehört
                $fileBelongsToUser = false;
                $csvFile = '../Uploaded_Files/files.csv'; // Pfad zur CSV-Datei mit den Dateiinformationen
                $fileData = array();
                if (($handle = fopen($csvFile, 'r')) !== false) {
                    while (($row = fgetcsv($handle)) !== false) {
                        if ($row[0] === $filename && $row[1] === $_SESSION['username']) {
                            $fileBelongsToUser = true;
                        } else {
                            $fileData[] = $row; // Füge die Zeile der CSV-Datei hinzu, es sei denn, sie gehört zur zu löschenden Datei
                        }
                    }
                    fclose($handle);
                } else {
                    echo "Fehler beim Öffnen der CSV-Datei: $csvFile";
                    exit;
                }

                if ($fileBelongsToUser) {
                    // Versuche, die Datei zu löschen
                    if (unlink($uploadDir . $filename)) {
                        // Die Datei wurde erfolgreich gelöscht
                        // Aktualisiere die CSV-Datei
                        if (($handle = fopen($csvFile, 'w')) !== false) {
                            foreach ($fileData as $row) {
                                fputcsv($handle, $row);
                            }
                            fclose($handle);
                            echo "Die Datei wurde erfolgreich gelöscht: $filename";
                        } else {
                            echo "Fehler beim Aktualisieren der CSV-Datei: $csvFile";
                        }
                    } else {
                        // Fehler beim Löschen der Datei
                        echo "Fehler beim Löschen der Datei: $filename";
                    }
                } else {
                    // Die Datei gehört nicht dem aktuellen Benutzer
                    echo "Die Datei gehört nicht Ihnen oder existiert nicht: $filename";
                }
            } else {
                // Ungültige Anfrage
                echo "Ungültige Anfrage";
                var_dump($_GET); // Ausgabe des GET-Arrays zur Fehlerdiagnose
            }
            ?>
            <div class="logout">
                <a href="index.php">Zurück zur Startseite</a>
            </div>
        </div>
    </main>
        <footer>
            <?php include("../templates/footeruser.php"); ?>
        </footer>
</body>
</html>
