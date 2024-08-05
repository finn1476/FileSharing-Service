<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Abuse</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        main {
            text-align: center;
        }

        .awasr {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 600px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
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
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<main>
    <div class="awasr">
        <div>
            <h2>Abuse</h2><br>
        </div>
        <div class="maske">
            <img src="bilder\vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/>
        </div>

        <p class="abusetextwidth">If you believe that some of our users are violating our terms of use or your intellectual property rights, please send us an email or fill out our report form.<br><br>

        Please provide accurate information about your identity, who you represent, and which file(s) this report pertains to.</p><br><br>
<div>
    <?php
        // Lesen der E-Mail-Adresse aus der CSV-Datei
        $csvFile = 'Speicher/email.csv'; // Pfad zur CSV-Datei
        $file = fopen($csvFile, 'r');
        $data = fgetcsv($file);
        fclose($file);

        $email = isset($data[0]) ? $data[0] : 'CHANGE@ME.de'; // Standard-E-Mail-Adresse, falls keine in der CSV-Datei gefunden wird
    ?>
    <a class="emailbutton" href="pgppublickey/key.asc">PGP Public Key</a>
    <a class="emailbutton" href="mailto:<?php echo $email; ?>?subject=Takedown Request">Email Us</a>
    <a class="emailbutton" href="report.php">Report Form</a>
</div>
        <footer>
        <?php include("templates/footer.php"); ?>
    </footer>
    </div>


</main>
</body>
</html>
