<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - Email-Adresse bearbeiten</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Hier die Styles aus der zweiten Datei einfügen */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        main {
            text-align: center;
            padding:10rem;
        }

      .awasr {
    border: 1px solid #ccc;
    padding: 20px;
    max-width: 600px;
    width: 100%;
    border-right: 1px solid #ccc; /* Neuer Rand auf der rechten Seite hinzugefügt */
}

        h2 {
            margin-bottom: 20px;
        }

        .impressum {
            text-align: left;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <?php
        // Funktion zum Abrufen der aktuellen E-Mail-Adresse aus der CSV-Datei
        function getCurrentEmail() {
            $csvFile = '../Speicher/email.csv';
            $currentEmail = '';

            if (file_exists($csvFile)) {
                $file = fopen($csvFile, 'r');
                if ($file !== FALSE) {
                    $currentEmail = fgets($file);
                    fclose($file);
                }
            }

            return $currentEmail;
        }

        // Aktuellen E-Mail-Wert abrufen
        $currentEmail = getCurrentEmail();
    ?>

    <main>
        <div class="awasr">
            <div>
                <h2>Change Email for Abuse Button</h2>
            </div>
            <form action="update_email.php" method="post">
                <label for="email">Change Email-Adresse:</label><br>
                <input type="email" id="email" name="email" value="<?php echo $currentEmail; ?>" required><br><br>
                <input type="submit" value="Aktualisieren">
            </form>
        </div>
    </main>
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
