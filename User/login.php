<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Registrierung und Anmeldung</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #333;
            color: #fff;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .awasr {
            background-color: #444;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 3px;
        }

        input[type="submit"] {
            background-color: #f00;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #c00;
        }

        /* Verstecke Formulare standardmäßig */
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <main>
        <div class="awasr">
            <h2>Registrierung</h2>
            <!-- Füge der Registrierungsform Klasse 'hidden' hinzu -->
            <form action="register.php" method="post" class="registration-form hidden">
                <input type="hidden" name="register" value="true">
                <input type="submit" value="Jetzt registrieren">
            </form>

            <h2>Anmeldung</h2>
            <!-- Füge der Anmeldeform Klasse 'hidden' hinzu -->
            <form action="login2.php" method="post" class="login-form hidden">
                <label for="username">Benutzername:</label>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Passwort:</label>
                <input type="password" id="password" name="password" required><br><br>
                <input type="submit" value="Anmelden">
            </form>

            <!-- Nachricht für deaktivierte Registrierung und Anmeldung -->
            <div class="deactivated-message hidden">
                <h2>Registration and login deactivated</h2>
                <p>Registration and login are currently disabled. Please try again later.</p>
            </div>
        </div>
    </main>

    <script>
        // Führe diese Funktion aus, wenn das Dokument vollständig geladen ist
        document.addEventListener("DOMContentLoaded", function() {
            // Hier kannst du den Pfad zur CSV-Datei angeben
            var csvFile = '../Speicher/userportal.csv';

            // Lese den Inhalt der CSV-Datei mittels XMLHttpRequest
            var request = new XMLHttpRequest();
            request.open('GET', csvFile, true);
            request.onreadystatechange = function() {
                if (request.readyState === XMLHttpRequest.DONE && request.status === 200) {
                    var csvData = request.responseText;

                    // Überprüfe, ob "1" in der CSV-Datei vorhanden ist
                    if (csvData.includes('1')) {
                        // Wenn "1" vorhanden ist, zeige die Formulare an
                        document.querySelectorAll('.registration-form, .login-form').forEach(function(form) {
                            form.classList.remove('hidden');
                        });
                    } else {
                        // Wenn "1" nicht vorhanden ist, zeige die Nachricht an und verstecke die Formulare
                        document.querySelector('.deactivated-message').classList.remove('hidden');
                        document.querySelectorAll('.registration-form, .login-form').forEach(function(form) {
                            form.classList.add('hidden');
                        });
                    }
                }
            };
            request.send();
        });
    </script>
    <footer class="footer">
    <?php 
	error_reporting(0);
	include("../templates/footeruser.php"); ?>
</footer>
</body>
</html>
