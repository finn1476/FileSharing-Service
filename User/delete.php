<?php
session_start();
?>
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
            

            if (!isset($_SESSION['username'])) {
                header("Location: index.html");
                exit;
            }

            require 'config.php'; // Konfigurationsdatei für die Datenbankverbindung einbinden

            if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['filename'])) {
                $filename = $_GET['filename'];
                $uploadDir = '../Files/'; // Verzeichnis, in dem die Dateien gespeichert sind

                // Überprüfen, ob die Datei existiert
                if (file_exists($uploadDir . $filename)) {
                    $fileSize = filesize($uploadDir . $filename);
                } else {
                    echo "Die Datei existiert nicht: $filename";
                    exit;
                }

                echo "Berechnete Dateigröße: $fileSize Bytes<br>";

                // Überprüfen, ob die Datei dem aktuellen Benutzer gehört
                $fileBelongsToUser = false;
                $csvFile = '../Uploaded_Files/files.csv'; // Pfad zur CSV-Datei mit den Dateiinformationen
                $fileData = array();

                // CSV-Datei aktualisieren
                if (($handle = fopen($csvFile, 'r')) !== false) {
                    while (($row = fgetcsv($handle)) !== false) {
                        if ($row[0] === $filename && $row[1] === $_SESSION['username']) {
                            $fileBelongsToUser = true;
                        } else {
                            $fileData[] = $row; // Füge die Zeile der CSV-Datei hinzu, es sei denn, sie gehört zur zu löschenden Datei
                        }
                    }
                    fclose($handle);

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

                                // Aktualisieren Sie die Datenbank, um die Datei und die Größe zu löschen
                                try {
                                    $pdo->beginTransaction();

                                    // Löschen Sie nur einen Eintrag aus der Tabelle uploads
                                    $sql = "DELETE FROM uploads 
                                            WHERE user_id = (SELECT id FROM users WHERE username = :username) 
                                            AND file_size = :file_size 
                                            LIMIT 1"; // Nur einen Eintrag löschen
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute([
                                        ':username' => $_SESSION['username'],
                                        ':file_size' => $fileSize
                                    ]);

                                    if ($stmt->rowCount() > 0) {
                                        // Eintrag erfolgreich gelöscht
                                        $pdo->commit();
                                        echo "Die Datei wurde erfolgreich gelöscht: $filename";
                                    } else {
                                        // Kein Eintrag gefunden
                                        $pdo->rollBack();
                                        echo "Kein Eintrag in der Datenbank gefunden oder bereits gelöscht.<br>";
                                        echo "Überprüfte Dateigröße: $fileSize Bytes";
                                    }
                                } catch (PDOException $e) {
                                    $pdo->rollBack();
                                    echo "Fehler beim Aktualisieren der Datenbank: " . $e->getMessage();
                                }
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
                    echo "Fehler beim Öffnen der CSV-Datei: $csvFile";
                    exit;
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
