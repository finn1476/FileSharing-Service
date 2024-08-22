<?php
require_once 'config.php'; // Datenbankkonfiguration einbinden

// Funktion zum Hinzufügen des Berichts zur SQL-Datenbank
function addReportToDatabase($email, $filenames, $description, $reason, $passwords) {
    global $pdo;

    $caseNumbers = []; // Array zur Speicherung der generierten Fallnummern

    // Sicherstellen, dass die Anzahl der Passwörter der Anzahl der Dateinamen entspricht
    if (count($filenames) != count($passwords)) {
        echo "Error: The number of filenames does not match the number of passwords.";
        return false;
    }

    foreach ($filenames as $index => $filename) {
        $caseNumber = generateCaseNumber(); // Generierung einer neuen Fallnummer für jeden Dateinamen
        $password = $passwords[$index]; // Das entsprechende Passwort für den Dateinamen holen

        try {
            // SQL-Abfrage zum Einfügen des Berichts
            $stmt = $pdo->prepare("
                INSERT INTO reports (case_number, email, filename, description, reason, passwords)
                VALUES (:case_number, :email, :filename, :description, :reason, :passwords)
            ");
            $stmt->execute([
                ':case_number' => $caseNumber,
                ':email' => $email,
                ':filename' => $filename,
                ':description' => $description,
                ':reason' => $reason,
                ':passwords' => $password
            ]);

            $caseNumbers[] = $caseNumber; // Hinzufügen der generierten Fallnummer zum Array
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    return $caseNumbers; // Rückgabe des Arrays mit allen generierten Fallnummern
}

// Funktion zur Generierung einer zufälligen 6-stelligen Fallnummer
function generateCaseNumber() {
    global $pdo;

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

// Funktion zur Überprüfung, ob die Fallnummer bereits in der Datenbank vorhanden ist
function isCaseNumberExists($caseNumber) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE case_number = :case_number");
    $stmt->execute([':case_number' => $caseNumber]);
    return $stmt->fetchColumn() > 0;
}

// Funktion zur Überprüfung, ob der Dateiname bereits existiert und aktiv ist
function isFilenameExists($filenames) {
    foreach ($filenames as $filename) {
        $filePath = 'Files/' . $filename;

        // Überprüfen, ob die Datei existiert
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false; // Die Datei existiert nicht oder ist nicht lesbar
        }
    }

    return true; // Alle Dateien existieren und sind lesbar
}

// Verarbeitung des Formulars beim Absenden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filenames = explode(',', $_POST["filenames"]);
    
    // Überprüfung, ob der Dateiname bereits existiert und aktiv ist
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

    // Holen der Passwörter aus dem Formular
    $passwords = explode(',', $_POST["passwords"]);

    // E-Mail und Beschreibung bereinigen
    $email = strip_tags($_POST["email"]);
    $description = strip_tags($_POST["description"]);

    // Hinzufügen des Berichts zur Datenbank
    $caseNumbers = addReportToDatabase($email, $filenames, $description, $reason, $passwords);

    if ($caseNumbers !== false) {
        // Erfolgsmeldung an den Benutzer
        echo "<script>
            alert('Report successfully created! Your case numbers are: " . implode(', ', $caseNumbers) . "');
            window.location.href = 'index.php';
            </script>";
    } else {
        // Fehlermeldung an den Benutzer
        echo "<script>
            alert('Error: Report could not be created.');
            window.location.href = 'report.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>File Report</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
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

      main {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
      }

      .awasr {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        width: 100%;
        border: 1px solid var(--border-color);
      }

      h1 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      form {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      label {
        font-weight: bold;
        margin-bottom: 5px;
      }

      input,
      textarea,
      select {
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        margin-top: 5px;
        width: 100%;
      }

      button {
        padding: 10px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s ease;
      }

      button:hover {
        background-color: var(--button-hover-color);
      }

      a {
        color: var(--primary-color);
        text-decoration: none;
      }

      a:hover {
        color: var(--accent-color);
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
    </style>
</head>
<body>
<header>
    <div class="logo">Anonfile</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="pricing.php">Pricing</a>
        <a href="User/login.php">Login</a>
    </nav>
</header>

<main>
    <div class="awasr">
        <h1>File Report</h1>
        <p>Use this form to report a file.</p>

        <form action="" method="post">
            <label for="emailInput">Email Address:</label>
            <input type="email" id="emailInput" name="email" required>

            <label for="reasonInput">Reason:</label>
            <select id="reasonInput" name="reason" required>
                <option hidden selected="true" value="Please select a reason" disabled>Please select a reason</option>
                <option value="inappropriate_content">Inappropriate Content</option>
                <option value="malicious_file">Malicious File</option>
                <option value="copyright_violation">Copyright Violation</option>
                <option value="Illegal under German law">Illegal under German law</option>
                <option value="Child Pornography">Child Pornography</option>
            </select>

            <label for="filenameInput">File Name(s) (comma-separated):</label>
            <input type="text" id="filenameInput" name="filenames" required>

            <label for="passwordInput">Decryption Password(s) (comma-separated):</label>
            <input type="text" id="passwordInput" name="passwords" required>

            <label for="descriptionInput">Description:</label>
            <textarea id="descriptionInput" name="description" required></textarea>

            <label for="datenschutz">I agree to the <a href="datenschutz.php">privacy policy</a>:
                <input type="checkbox" required name="datenschutz" value="" />
            </label>

            <?php
            $datei = fopen("Speicher/reportstatus.csv", "r");
            $aktiv = fgets($datei, 10);
            fclose($datei);

            if ($aktiv == 1) {
                echo "<button type='submit'>Submit Report</button>";
            } else {
                echo "<div>Submissions via this form are disabled.</div>";
            }
            ?>
        </form>
    </div>
</main>

  <footer class="footer">
    <div class="footer-links">
      <a href="FAQ.php">FAQ</a>
      <a href="impressum.php">Imprint</a>
     <a href="abuse.php">Abuse</a>
	 <a href="terms.php">ToS</a>
      <a href="datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
  </footer>
</body>
</html>
