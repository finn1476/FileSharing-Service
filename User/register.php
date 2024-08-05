<?php
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = generateRandomString();
    $password = generateRandomString();

    // Pfad zur .htpasswd-Datei
    $htpasswd_file = __DIR__ . '/password/.htpasswd';

    // Überprüfen, ob das Verzeichnis existiert
    if (!is_dir(__DIR__ . '/password')) {
        if (!mkdir(__DIR__ . '/password', 0755, true)) {
            die('Fehler beim Erstellen des Verzeichnisses.');
        }
    }

    // Überprüfen, ob die Datei schreibbar ist oder erstellt werden kann
    if (!is_writable($htpasswd_file)) {
        if (file_exists($htpasswd_file)) {
            die('Die .htpasswd-Datei ist nicht schreibbar.');
        } else {
            if (file_put_contents($htpasswd_file, "") === false) {
                die('Fehler beim Erstellen der .htpasswd-Datei.');
            }
        }
    }

    // Passwort verschlüsseln
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Benutzername und Passwort zur .htpasswd-Datei hinzufügen
    $htpasswd_entry = "$username:$hashed_password\n";
    if (file_put_contents($htpasswd_file, $htpasswd_entry, FILE_APPEND | LOCK_EX) === false) {
        die('Fehler beim Schreiben in die .htpasswd-Datei.');
    } else {
        echo "<!DOCTYPE html>
<html lang='de'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Registrierung Erfolgreich</title>
    <link rel='stylesheet' href='style.css'>
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

        p {
            margin-bottom: 10px;
        }

        button {
            background-color: #f00;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 5px;
        }

        button:hover {
            background-color: #c00;
        }
    </style>
</head>
<body>
    <main>
        <div class='awasr'>
            <h2>Registrierung erfolgreich!</h2>
            <p>Benutzername: <span id='username'>$username</span> <button onclick='copyToClipboard(\"username\")'>Kopieren</button></p>
            <p>Passwort: <span id='password'>$password</span> <button onclick='copyToClipboard(\"password\")'>Kopieren</button></p>
            <p><a href='login.php'>Zur Anmeldeseite</a></p>
        </div>
    </main>
    <script>
        function copyToClipboard(id) {
            var copyText = document.getElementById(id).innerText;
            navigator.clipboard.writeText(copyText).then(function() {
                alert('Kopiert: ' + copyText);
            }, function(err) {
                alert('Fehler beim Kopieren: ' + err);
            });
        }
    </script>
</body>
</html>";
    }
} else {
    echo "Ungültige Anfrage.";
}
?>
