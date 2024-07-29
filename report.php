<?php
// Funktion zum Hinzufügen des Berichts zur CSV-Datei
function addReportToCSV($email, $filenames, $description, $reason) {
    $csvFile = 'sicherspeicher/reports.csv';
    $maxAttempts = 15;
    $attempt = 0;

    do {
        // Sperren der Datei für den Schreibzugriff
        $fileHandle = fopen($csvFile, 'a');
        if (!$fileHandle) {
            // Fehlerbehandlung, falls die Datei nicht geöffnet werden kann
            return false;
        }

        if (flock($fileHandle, LOCK_EX)) { // Sperren der Datei
            // Speichern der Berichtsdetails in der CSV-Datei
            $caseNumbers = []; // Array zur Speicherung der generierten Fallnummern

            foreach ($filenames as $filename) {
                $id = getNextID(); // Generierung der ID für jeden Dateinamen
                $caseNumber = generateCaseNumber(); // Generierung einer neuen Fallnummer für jeden Dateinamen
                $reportData = "$id,$caseNumber,$email,$filename,$description,$reason" . PHP_EOL;
                fwrite($fileHandle, $reportData); // Hinzufügen der Berichtsdetails
                $caseNumbers[] = $caseNumber; // Hinzufügen der generierten Fallnummer zum Array
            }

            flock($fileHandle, LOCK_UN); // Freigeben der Datei
            fclose($fileHandle); // Datei schließen

            return $caseNumbers; // Rückgabe des Arrays mit allen generierten Fallnummern
        } else {
            // Fehlerbehandlung, falls die Datei nicht gesperrt werden kann
            fclose($fileHandle); // Datei schließen
            $attempt++;
            usleep(1000000); // 1 Sekunde warten
        }
    } while ($attempt < $maxAttempts);

    // Falls nach $maxAttempts Versuchen die Datei nicht gesperrt werden kann
    echo "<script>alert('Error: Could not acquire lock for file.');</script>";
    return false;
}


// Funktion zum Abrufen der nächsten verfügbaren ID
function getNextID() {
    $csvFile = 'sicherspeicher/reports.csv';

    // Wenn die Datei nicht existiert oder leer ist, beginne mit ID 1
    if (!file_exists($csvFile) || empty(file_get_contents($csvFile))) {
        return "1";
    }

    // Andernfalls, alle IDs aus der CSV-Datei lesen
    $csvData = file_get_contents($csvFile);
    $lines = explode(PHP_EOL, $csvData);
    $ids = [];

    foreach ($lines as $line) {
        $data = explode(',', $line);
        $id = intval($data[0]);
        $ids[] = $id;
    }

    // Die nächste verfügbare ID ist die größte ID plus 1
    $nextID = max($ids) + 1;
    return strval($nextID);
}

// Funktion zur Generierung einer zufälligen 6-stelligen Fallnummer
function generateCaseNumber() {
    $maxAttempts = 50;

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $caseNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        // Überprüfen, ob die generierte Fallnummer bereits existiert
        if (!isCaseNumberExists($caseNumber)) {
            return $caseNumber;
        }
    }

    // Wenn nach 50 Versuchen keine eindeutige Fallnummer gefunden wird, gibt es einen Fehler
    return null;
}

// Funktion zur Überprüfung, ob die Fallnummer bereits in der CSV-Datei vorhanden ist
function isCaseNumberExists($caseNumber) {
    $csvFile = 'sicherspeicher/reports.csv';
    $csvData = file_get_contents($csvFile);
    $lines = explode(PHP_EOL, $csvData);

    foreach ($lines as $line) {
        $data = explode(',', $line);
        $existingCaseNumber = isset($data[1]) ? $data[1] : null;

        if ($existingCaseNumber === $caseNumber) {
            return true; // Die Fallnummer existiert bereits
        }
    }

    return false; // Die Fallnummer ist eindeutig
}

// Funktion zur Überprüfung, ob der Dateiname bereits in der CSV-Datei vorhanden ist und aktiv ist
function isFilenameExists($filenames) {
    foreach ($filenames as $filename) {
        $filePath = 'Files/' . $filename;  // Updated path to check inside the "/files" directory

        // Überprüfen, ob die Datei existiert
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false; // Die Datei existiert nicht oder ist nicht lesbar
        }
    }

    return true; // Alle Dateien existieren und sind lesbar
}

// Verarbeitung des Formulars beim Absenden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = getNextID();

    // Generierung der Fallnummer und Überprüfung der Eindeutigkeit
    $caseNumber = generateCaseNumber();

    // Überprüfung, ob der Dateiname bereits existiert und aktiv ist
    $filenames = explode(',', $_POST["filenames"]);
    if (!isFilenameExists($filenames)) {
        // Fehlermeldung an den Benutzer
        echo "<script>
            alert('Error: One or more files do not exist.');
            window.location.href = 'report.php';
        </script>";
        exit; // Abbruch des Skripts
    }

    // Überprüfung, ob der Benutzer "Please select a reason" ausgewählt hat
    $reason = $_POST["reason"];
    if ($reason === "Please select a reason") {
        // Fehlermeldung an den Benutzer
        echo "<script>
            alert('Error: Please select a reason.');
            window.location.href = 'report.php';
        </script>";
        exit; // Abbruch des Skripts
    }

if ($caseNumber !== null) {
    // Wenn eine eindeutige Fallnummer gefunden wurde
    $email = strip_tags($_POST["email"]);
    $description = strip_tags($_POST["description"]);

    // Hinzufügen des Berichts zur CSV-Datei
    $caseNumbers = addReportToCSV($email, $filenames, $description, $reason);

    // Erfolgsmeldung an den Benutzer
    echo "<script>
        alert('Report successfully created! Your case numbers are: " . implode(', ', $caseNumbers) . "');
        window.location.href = 'index.php';
        </script>";
    } else {
        // Fehlermeldung an den Benutzer
        echo "<script>
            alert('Error: No unique case number could be generated.');
            window.location.href = 'report.php';
        </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>File Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="noindex">
    <link rel="stylesheet" type="text/css" href="style.css" />
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: black;
            margin: 0;
            padding: 0;
        }
		footer{
			font-family: monospace;
		}
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .awasr {
            background-color: black;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
			border:solid 1px white;
        }

        h1 {
            text-align: center;
            color: white;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-weight: bold;
        }

        input,
        textarea,
        select,
        button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }
		a{
			text-decoration:none;
			color: white;
		}
		a:hover{
			text-decoration:none;
			color: grey;
		}

    </style>
</head>

<body>
    <main >


        <div class="awasr">
            <h1>File Report</h1>
            <p>Use this form to report a file.</p>

            <form action="" method="post">
                <!-- ID wird hier nicht angezeigt, aber im PHP-Code verwendet -->
                <label for="emailInput">Email Address:</label>
                <input type="email" id="emailInput" name="email" required>

				<label for="reasonInput">Reason:</label>
                <select id="reasonInput" name="reason" required>
				<option value="Please select a reason">Please select a reason</option>
                    <option value="inappropriate_content">Inappropriate Content</option>
					<option value="malicious_file">Malicious File</option>
                    <option value="copyright_violation">Copyright Violation</option>
					<option value="Illegal under German law">Illegal under German law</option>
                    <option value="Child Porno Pornography">Child Pornography</option>

                    <!-- Add more reasons as needed -->
                </select>

                <label for="filenameInput">File Name(s) (comma-separated):</label>
                <input type="text" id="filenameInput" name="filenames" required>

                <label for="descriptionInput">Description:</label>
                <textarea id="descriptionInput" name="description" required></textarea>
				<label for="datenschutz">I agree to the <a href="datenschutz.php">privacy policy</a>:
				<input type="checkbox" required name="datenschutz" value="" /></label>

              <?php
				  $datei = fopen("Speicher/reportstatus.csv","r");
				  $aktiv = fgets($datei, 10);
				  fclose($datei);


				if ($aktiv == 1)
				{
					echo"<button type='submit'>Submit Report</button>";

				}else{
					echo"<div>Submissions via this form are disabled.</div>";
				}

			  ?>


            </form>
        </div>
    </main>
	<footer>
            <?php include("templates/footer.php"); ?>
        </footer>
</body>

</html>
