<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Impressum</title>

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

      .impressum {
        text-align: left;
        margin-top: 30px;
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
        <h2>Impressum</h2>

        <div class="impressum">
            <p><strong>Diensteanbieter:</strong><br>
                <?php
                $csv = "Speicher/impressum.csv"; // Korrekter Pfad zur CSV-Datei

                // CSV Datei einlesen
                if (($csvFile = fopen($csv, 'r')) !== FALSE) {
                    // Daten aus CSV lesen und anzeigen
                    while (($line = fgetcsv($csvFile)) !== FALSE) {
                        echo $line[0] . '<br>';
                    }
                    // CSV Datei schließen
                    fclose($csvFile);
                } else {
                    echo "Fehler beim Öffnen der CSV-Datei.";
                }
                ?>
            </p>

            <p><strong>Kontaktmöglichkeiten:</strong><br>
                <?php
                // CSV Datei erneut öffnen, um Kontaktinformationen zu lesen
                if (($csvFile = fopen($csv, 'r')) !== FALSE) {
                    // Kontaktinformationen aus CSV lesen und anzeigen
                    while (($line = fgetcsv($csvFile)) !== FALSE) {
                        echo 'E-Mail-Adresse: <a href="mailto:' . $line[1] . '">' . $line[1] . '</a><br>';
                        echo 'Telefon: ' . $line[2] . '<br>';
                    }
                    // CSV Datei schließen
                    fclose($csvFile);
                } else {
                    echo "Fehler beim Öffnen der CSV-Datei.";
                }
                ?>
            </p>

            <p><strong>Haftungs- und Schutzrechtshinweise:</strong><br>
                <?php
                // CSV Datei erneut öffnen, um Haftungsausschluss zu lesen
                if (($csvFile = fopen($csv, 'r')) !== FALSE) {
                    // Haftungsausschluss aus CSV lesen und anzeigen
                    while (($line = fgetcsv($csvFile)) !== FALSE) {
                        echo $line[3] . '<br>';
                    }
                    // CSV Datei schließen
                    fclose($csvFile);
                } else {
                    echo "Fehler beim Öffnen der CSV-Datei.";
                }
                ?>
            </p>
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
