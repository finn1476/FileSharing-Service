<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Files</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script>
        function confirmDelete() {
            return confirm("Do you really want to delete these files?");
        }
        function toggleAll(source) {
            checkboxes = document.getElementsByName('deleteFiles[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }

    </script>
</head>
<body>
<div class="awasr">
    <h1>Delete Files</h1>
    <form id="selectAgeForm" method="post">
        <label for="maxAge">Select the maximum age of the file (in days):</label>
        <div>
            <button type="submit" name="maxAge" value="30">30 Days</button>
            <button type="submit" name="maxAge" value="60">60 Days</button>
            <button type="submit" name="maxAge" value="90">90 Days</button>
            <button type="submit" name="maxAge" value="120">120 Days</button>
            <button type="submit" name="addNewFiles">Set all dates to current date</button>
        </div>
    </form>
    <a class="bauttona" href="deactivateoldfilecollection.php">Options Files</a>
    <div id="result">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // CSV-Datei laden
            $csvFile = '../Uploaded_Files/uploaded_files.csv';
            $fileData = file($csvFile);
            $currentDate = new DateTime();
            $newFilesAdded = 0;

            if (isset($_POST["addNewFiles"])) {
                // Alle Dateien in /Files/ durchgehen
                $filesInFolder = glob('../Files/*');
                foreach ($filesInFolder as $file) {
                    $fileName = basename($file);
                    // Prüfen, ob die Datei bereits in der CSV enthalten ist
                    $fileExistsInCSV = false;
                    foreach ($fileData as $line) {
                        $columns = explode(',', $line);
                        if (trim($columns[0]) === $fileName) {
                            $fileExistsInCSV = true;
                            break;
                        }
                    }
                    // Datei in die CSV-Datei schreiben, wenn nicht vorhanden
                    if (!$fileExistsInCSV) {
                        $newFileData[] = "$fileName," . $currentDate->format('Y-m-d') . PHP_EOL;
                        $newFilesAdded++;
                    }
                }
                // CSV-Datei mit den neuen Datei-Daten aktualisieren
                if (!empty($newFileData)) {
                    file_put_contents($csvFile, implode('', $fileData) . implode('', $newFileData));
                }
            }

            // Wenn das maximale Alter ausgewählt wurde
            if (isset($_POST["maxAge"])) {
                // Parameter aus dem Formular holen
                $maxAge = $_POST["maxAge"];
                $oldFiles = [];

                foreach ($fileData as $line) {
                    $columns = explode(',', $line);
                    if (count($columns) === 2) {
                        $fileName = trim($columns[0]);
                        $createdDate = new DateTime(trim($columns[1]));
                        $ageInDays = $createdDate->diff($currentDate)->days;

                        if ($ageInDays > $maxAge) {
                            $fileSize = filesize('../Files/' . $fileName);
                            if ($fileSize !== false) {
                                $oldFiles[] = [
                                    'name' => $fileName,
                                    'age' => $ageInDays,
                                    'size' => $fileSize / (1024 * 1024)
                                ];
                            }
                        }
                    }
                }

                // Zeige alte Dateien mit Checkboxen
                if (!empty($oldFiles)) {
                    echo '<form id="deleteForm" method="post" onsubmit="return confirmDelete()">';
                    echo '<table border="1">';
                    echo '<tr><th><input type="checkbox" onclick="toggleAll(this)"></th><th>Filename</th><th>Age (in Days)</th><th>File size (in MB)</th></tr>';
                    foreach ($oldFiles as $file) {
                        echo '<tr>';
                        echo '<td><input type="checkbox" name="deleteFiles[]" value="' . $file['name'] . '"></td>';
                        echo '<td>' . $file['name'] . '</td>';
                        echo '<td>' . $file['age'] . '</td>';
                        echo '<td>' . number_format($file['size'], 2) . '</td>'; // Korrektur hier
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '<input type="hidden" name="maxAge" value="' . $maxAge . '">';
                    echo '<button type="submit" name="deleteSelected">Delete</button>';
                    echo '</form>';
                }
            }
            if (isset($_POST["addNewFiles"]) && $newFilesAdded > 0) {
                // Anzeigen, dass neue Dateien hinzugefügt wurden
                echo "<p>$newFilesAdded neue Dateien wurden hinzugefügt.</p>";
            }
        }
          // Verarbeitung der Löschanfragen
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteSelected"])) {
            $deletedFiles = $_POST["deleteFiles"];
            $filesToDelete = 0;
            $spaceFreed = 0;
            $currentDate = new DateTime();

            // CSV-Datei laden
            $csvFile = '../Uploaded_Files/uploaded_files.csv';
            $fileData = file($csvFile);
            $newFileData = [];

            foreach ($fileData as $line) {
                $columns = explode(',', $line);
                if (count($columns) === 2) {
                    $fileName = trim($columns[0]);
                    if (in_array($fileName, $deletedFiles)) {
                        $fileSize = filesize('../Files/' . $fileName);
                        if ($fileSize !== false) {
                            $filesToDelete++;
                            $spaceFreed += $fileSize / (1024 * 1024);
                            unlink('../Files/' . $fileName); // Datei löschen
                        }
                    } else {
                        $newFileData[] = $line;
                    }
                }
            }

            // CSV-Datei mit den neuen Datei-Daten aktualisieren
            file_put_contents($csvFile, implode('', $newFileData));

            // Ergebnis anzeigen
            echo "Number of Deleted Files: $filesToDelete<br>";
            echo "Freed Space (in MB): " . number_format($spaceFreed, 2) . "<br><br>";

            // Weiterleitung nach 15 Sekunden
            header("refresh:15;url=index.php");
        }
        ?>
    </div>
</div>
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
