<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
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
    <main>
        <div class="awasr">
            <div>
                <h2>Impressum bearbeiten</h2>
                <?php
                // CSV-Datei lesen und aktuellen Inhalt des Impressums abrufen
                $csvFile = fopen('../Speicher/impressum.csv', 'r');
                if ($csvFile !== FALSE) {
                    $impressumData = fgetcsv($csvFile); // Erste Zeile lesen (Inhalt des Impressums)
                    fclose($csvFile);
                }
                ?>
                <form action="update_impressum2.php" method="post">
                    <label for="name">Name:</label><br>
                    <input type="text" id="name" name="name" value="<?php echo isset($impressumData[0]) ? $impressumData[0] : ''; ?>"><br>

                    <label for="email">E-Mail-Adresse:</label><br>
                    <input type="email" id="email" name="email" value="<?php echo isset($impressumData[1]) ? $impressumData[1] : ''; ?>"><br>

                    <label for="phone">Telefon:</label><br>
                    <input type="text" id="phone" name="phone" value="<?php echo isset($impressumData[2]) ? $impressumData[2] : ''; ?>"><br>

                    <label for="disclaimer">Haftungsausschluss:</label><br>
                    <textarea id="disclaimer" name="disclaimer"><?php echo isset($impressumData[3]) ? $impressumData[3] : ''; ?></textarea><br>

                    <center><input type="submit" value="Impressum aktualisieren"></center>
                </form>


            </div>
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
