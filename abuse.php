<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Abuse</title>

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

      .maske {
        margin-bottom: 20px;
      }

      .abusetextwidth {
        text-align: left;
        margin-bottom: 20px;
      }

      .emailbutton {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        background-color: var(--button-color);
        color: white;
        border-radius: 5px;
        margin: 5px;
        transition: background-color 0.3s ease;
      }

      .emailbutton:hover {
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
    </style>
</head>
<body>
<header>
    <div class="logo">Anonfile</div>
<?php

include("templates/header.php");	
	
?>
</header>

<main>
    <div class="awasr">
        <h2>Abuse</h2>

        

        <p class="abusetextwidth">If you believe that some of our users are violating our terms of use or your intellectual property rights, please send us an email or fill out our report form.<br><br>
        Please provide accurate information about your identity, who you represent, and which file(s) this report pertains to.</p>
        
        <div>
            <?php
                // Lesen der E-Mail-Adresse aus der CSV-Datei
                $csvFile = 'Speicher/email.csv'; // Pfad zur CSV-Datei
                $file = fopen($csvFile, 'r');
                $data = fgetcsv($file);
                fclose($file);

                $email = isset($data[0]) ? $data[0] : 'CHANGE@ME.de'; // Standard-E-Mail-Adresse, falls keine in der CSV-Datei gefunden wird
            ?><center>
            <a class="emailbutton" href="pgppublickey/key.asc">PGP Public Key</a>
            <a class="emailbutton" href="mailto:<?php echo $email; ?>?subject=Takedown Request">Email Us</a>
            <a class="emailbutton" href="report.php">Report Form</a></center>
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
