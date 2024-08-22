<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - Datenschutzerklärung bearbeiten</title>
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
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        background-color: var(--background-color);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .impressum {
        margin-top: 30px;
      }

      .impressum label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }

      .impressum input,
      .impressum textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        box-sizing: border-box;
        margin-bottom: 15px;
        font-size: 16px;
      }

      .impressum textarea {
        height: 150px;
        resize: vertical;
      }

      .custom-file-upload {
        cursor: pointer;
        background-color: var(--button-color);
        color: white;
        padding: 15px 30px;
        border-radius: 5px;
        border: none;
        font-size: 18px;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .custom-file-upload:hover {
        background-color: var(--button-hover-color);
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

      @media (max-width: 600px) {
        nav {
          flex-direction: column;
          gap: 10px;
        }

        .footer-links {
          flex-direction: column;
          gap: 10px;
        }
      }

      input[type="file"] {
        display: none;
      }
    </style>
</head>
<body>
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

    <main>
        <div class="awasr">
            <h2>Datenschutzerklärung bearbeiten</h2>
            <?php
            // Überprüfen, ob das Formular gesendet wurde
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Daten aus dem Formular erhalten
                $kontaktdaten = $_POST["kontaktdaten"];
                $speicherung_deutsch = $_POST["speicherung_deutsch"];
                $speicherung_englisch = $_POST["speicherung_englisch"];

                // Daten in CSV-Format konvertieren
                $data = array($kontaktdaten, $speicherung_deutsch, $speicherung_englisch);
                $dataLine = implode(",", $data) . PHP_EOL;

                // Pfad zur CSV-Datei
                $csvFilePath = "../Speicher/datenschutzerklaerung.csv";

                // CSV-Datei öffnen oder erstellen
                $csvFile = fopen($csvFilePath, "w");

                // Daten in die CSV-Datei schreiben
                fwrite($csvFile, $dataLine);

                // CSV-Datei schließen
                fclose($csvFile);

                echo "<p>Datenschutzerklärung erfolgreich aktualisiert.</p>";
            }
            ?>
            <?php
            // Aktuelle Werte aus der CSV-Datei lesen
            $csvFile = fopen('../Speicher/datenschutzerklaerung.csv', 'r');
            if ($csvFile !== FALSE) {
                $data = fgetcsv($csvFile); // Erste Zeile lesen
                fclose($csvFile);
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="impressum">
                <label for="kontaktdaten">Kontaktdaten:</label>
                <textarea id="kontaktdaten" name="kontaktdaten" rows="4" cols="50"><?php echo isset($data[0]) ? $data[0] : ''; ?></textarea>

                <label for="speicherung_deutsch">Welche Daten speichern wir? (Deutsch):</label>
                <textarea id="speicherung_deutsch" name="speicherung_deutsch" rows="4" cols="50"><?php echo isset($data[1]) ? $data[1] : ''; ?></textarea>

                <label for="speicherung_englisch">What data do we store? (Englisch):</label>
                <textarea id="speicherung_englisch" name="speicherung_englisch" rows="4" cols="50"><?php echo isset($data[2]) ? $data[2] : ''; ?></textarea>

                <input type="submit" value="Aktualisieren" class="custom-file-upload">
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-links">
            <a href="index.php">Linkpage</a>
            <a href="../index.php">Home</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
    
    <script>
    function _(el) {
      return document.getElementById(el);
    }

    function showCookieBanner() {
      if (!localStorage.getItem('cookiesAccepted')) {
        document.getElementById('cookieBanner').style.display = 'block';
      }
    }

    function acceptCookies() {
      localStorage.setItem('cookiesAccepted', 'true');
      document.getElementById('cookieBanner').style.display = 'none';
    }

    // Display the cookie banner
    showCookieBanner();
    </script>
</body>
</html>
