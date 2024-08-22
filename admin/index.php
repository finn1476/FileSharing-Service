<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>Admin Panel</title>
    <style>
        /* Farbvariablen */
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

        /* Grundlegendes Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        /* Hauptbereich */
        main {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Warnmeldung */
        .warning {
            color: var(--error-color);
            background-color: var(--background-color);
            padding: 20px;
            border: 1px solid var(--error-color);
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .warning h3 {
            margin: 0 0 10px;
            font-size: 20px;
        }

        .warning button {
            background-color: var(--button-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }

        .warning button:hover {
            background-color: var(--button-hover-color);
        }

        /* Upload-Container */
        .upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: var(--background-color);
            padding: 2rem;
            border: 2px dashed var(--border-color);
            border-radius: 10px;
            transition: border-color 0.3s ease;
            text-align: center;
            margin-bottom: 30px;
        }

        .upload-container:hover {
            border-color: var(--accent-color);
        }

        /* Buttons */
        .buttona {
            display: inline-block;
            background-color: var(--button-color);
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin: 8px;
            transition: background-color 0.3s ease;
        }

        .buttona:hover {
            background-color: var(--button-hover-color);
        }

        /* Footer */
        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 30px;
            text-align: center;
            border-top: 4px solid var(--secondary-color);
            position: relative;
        }

        .footer a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            margin: 0 15px;
        }

        .footer a:hover {
            color: var(--accent-color);
        }

        .footer .bauttona {
            background-color: var(--button-color);
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin: 5px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .footer .bauttona:hover {
            background-color: var(--button-hover-color);
        }

        /* Bild */
        .maske img {
            max-width: 80%;
            height: auto;
            border-radius: 10px;
        }

        .pictureguy {
            width: 250px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function hideWarning() {
            document.getElementById('warning_message').style.display = 'none';
        }
    </script>
</head>
<body>
<main>
    <?php
    // Read expiry timestamp from file_expiry.csv
    $csvFile = '../Speicher/file_expiry.csv';
    $csvContent = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $expiryTimestamp = strtotime(explode(',', $csvContent[0])[1]); // Extracting the expiry timestamp from CSV line

    // Current timestamp
    $now = time();

    // Calculate remaining days
    $remainingDays = ceil(($expiryTimestamp - $now) / (60 * 60 * 24));

    // Check if warning should be displayed
    if ($remainingDays <= 10) {
        echo '<div id="warning_message" class="warning">';
        echo '<h3>Warning!</h3>';
        echo "<p>Publix PGP Key will expire in $remainingDays days. Do you want to Ignore it or Fix it?</p>";
        echo '<button onclick="hideWarning()">Ignore</button>';
        echo '<button onclick="window.location.href = \'adminpanelpgpschlusel.php\'">Fix</button>';
        echo '</div>';
    }
    ?>

    <div class="upload-container">
        <h2>Anonymer File Upload</h2>
        
        <h1>Admin Panel</h1>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <label for="csvFile">CSV Block Datei hochladen:</label><br>
            <input type="file" name="csvFile" id="csvFile" accept=".csv" >
            <input type="submit" name="updateFromCSV" value="Aktualisieren von CSV">
        </form>

        <p>
            <a class="buttona" href="../Speicher/hashes.csv">Download Block CSV Datei</a>
            <a class="buttona" href="../index.php">HOME</a>
        </p>

        <p>
            <a class="buttona" href="adminpanel2.php">Nächste Seite</a>
        </p>

        <p>
            <a class="buttona" href="update_impressum.php">Edit Imprint</a>
            <a class="buttona" href="update_datenschutz.php">Edit Privacy Notice</a>
            <a class="buttona" href="email.php">Abuse Email</a>
            <a class="buttona" href="monero.php">Donation</a>
            <a class="buttona" href="Deletefiles.php">Delete Old Files</a>
            <a class="buttona" href="userportal.php">Userportal</a>
            <br><br>
            <a class="buttona" href="uploadlimit.php">Upload Limit</a>
            <a class="buttona" href="admin_gift_cards.php">Coupons</a>
            <a class="buttona" href="subscrition.php">Remove Expired Subscriptions</a>
            <a class="buttona" href="setup.php">Setup</a>
        </p>
    </div>
</main>

<footer class="footer">
    <div class="footer-links">
        <a class="bauttona" href="adminpanel5.php">Statistiken</a>
        <a class="bauttona" href="adminpanel4.php">Datei-Typen</a>
        <a class="bauttona" href="adminpanel3.php">Benutzer-Verwaltung</a>
        <a class="bauttona" href="adminpanel2.php">Upload-Grenze</a>
    </div>
    <div>
        <a class="bauttona" href="admindelete.php">Löschen</a>
    </div>
</footer>
</body>
</html>
