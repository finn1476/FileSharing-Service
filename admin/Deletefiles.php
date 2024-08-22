<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Files</title>

    <style>
        :root {
            --primary-color: #005f73;
            --secondary-color: #94d2bd;
            --accent-color: #ee9b00;
            --background-color: #f7f9fb;
            --text-color: #023047;
            --muted-text-color: #8e9aaf;
            --border-color: #d9e2ec;
            --button-color: #56cfe1;
            --button-hover-color: #028090;
            --error-color: #e63946;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .awasr {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        button {
            background-color: var(--button-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 5px;
        }

        button:hover {
            background-color: var(--button-hover-color);
        }

        a.bauttona {
            display: inline-block;
            margin-top: 20px;
            color: var(--button-color);
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            border-bottom: 2px solid var(--button-color);
        }

        a.bauttona:hover {
            border-bottom: 2px solid var(--accent-color);
            color: var(--accent-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid var(--border-color);
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: var(--secondary-color);
            color: var(--text-color);
        }

        td {
            background-color: white;
        }

        footer {
            background-color: var(--primary-color);
            padding: 20px;
            color: white;
            text-align: center;
            border-top: 3px solid var(--secondary-color);
        }

        footer .footer-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        footer .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        footer .footer-links a:hover {
            color: var(--accent-color);
        }
		        header {
            background-color: var(--primary-color);
            padding: 10px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--secondary-color);
        }

        header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
        }

        nav a:hover {
            color: var(--accent-color);
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Do you really want to delete these files?");
        }

        function toggleAll(source) {
            const checkboxes = document.getElementsByName('deleteFiles[]');
            for(let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<header>
        <div class="logo">Admin Panel</div>
        <nav>
            <a href="adminpanel5.php">Statistiken</a>
            <a href="adminpanel4.php">Datei-Typen</a>
            <a href="adminpanel3.php">Benutzer-Verwaltung</a>
            <a href="adminpanel2.php">Upload-Grenze</a>
            <a href="admindelete.php">Löschen</a>
        </nav>
    </header>
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
                <button type="submit" name="cleanupCSV">Cleanup CSV</button>
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
                        echo '<table>';
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

                // CSV bereinigen
                if (isset($_POST["cleanupCSV"])) {
                    $cleanedFileData = [];
                    $removedFilesCount = 0;

                    foreach ($fileData as $line) {
                        $columns = explode(',', $line);
                        $fileName = trim($columns[0]);

                        // Prüfen, ob die Datei noch existiert
                        if (file_exists('../Files/' . $fileName)) {
                            $cleanedFileData[] = $line;
                        } else {
                            $removedFilesCount++;
                        }
                    }

                    // Aktualisiere die CSV-Datei
                    file_put_contents($csvFile, implode('', $cleanedFileData));

                    // Ergebnis anzeigen
                    echo "<p>$removedFilesCount Dateien, die nicht mehr existierten, wurden aus der CSV entfernt.</p>";
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
    <footer class="footer">
        <div class="footer-links">
            <a href="index.php">Linkpage</a>
            <a href="../index.php">Home</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
</body>
</html>
