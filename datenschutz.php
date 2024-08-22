<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Datenschutzerklärung</title>

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
        
       
        text-align: left;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        max-width: 100%;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      .impressum p {
        margin-bottom: 20px;
      }

      .impressum strong {
        color: var(--primary-color);
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
        <h2>Datenschutzerklärung</h2>

        <div class="impressum">
            <?php
                $csv = "Speicher/datenschutzerklaerung.csv"; // Pfad zur CSV-Datei

                // CSV-Datei einlesen
                if (($csvFile = fopen($csv, 'r')) !== FALSE) {
                    // Überprüfen, ob die CSV-Datei Daten enthält
                    $hasData = false;
                    // Daten aus CSV lesen und anzeigen
                    while (($line = fgetcsv($csvFile)) !== FALSE) {
                        if (!empty($line)) {
                            $hasData = true;
                            // Kontaktdaten Überschrift
                            if (isset($line[0])) {
                                echo "<p><strong>Kontaktdaten</strong><br>";
                                echo htmlspecialchars($line[0]) . "</p>";
                            }
                            // Welche Daten speichern wir? Überschrift
                            if (isset($line[1])) {
                                echo "<p><strong>Welche Daten speichern wir?</strong><br>";
                                echo htmlspecialchars($line[1]) . "</p>";
                            }
                            // What data do we store? Überschrift
                            if (isset($line[2])) {
                                echo "<p><strong>What data do we store?</strong><br>";
                                echo htmlspecialchars($line[2]) . "</p>";
                            }
                        }
                    }
                    if (!$hasData) {
                        echo "<p>Keine Daten verfügbar.</p>";
                    }
                    // CSV-Datei schließen
                    fclose($csvFile);
                } else {
                    echo "<p>Fehler beim Öffnen der CSV-Datei.</p>";
                }
            ?>
        </div>



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
