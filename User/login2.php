<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validierung der Eingaben
    if (empty($username) || empty($password)) {
        die("Benutzername und Passwort dürfen nicht leer sein.");
    }

    // Pfad zur .htpasswd-Datei
    $htpasswd_file = __DIR__ . '/password/.htpasswd';

    // Überprüfen, ob die .htpasswd-Datei existiert
    if (!file_exists($htpasswd_file)) {
        die("Keine Benutzer gefunden.");
    }

    // Lesen der .htpasswd-Datei
    $lines = file($htpasswd_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        list($stored_username, $stored_hashed_password) = explode(':', $line);

        if ($username === $stored_username) {
            if (password_verify($password, $stored_hashed_password)) {
                $_SESSION['username'] = $username;
                header("Refresh: 5; url=index.php");
                die("<!DOCTYPE html>
<html lang='de'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Anmeldung Erfolgreich</title>
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
    </style>
</head>
<body>
    <main>
        <div class='awasr'>
            <h2>Anmeldung erfolgreich!</h2>
            <p>Sie werden in 5 Sekunden weitergeleitet...</p>
        </div>
    </main>
</body>
</html>");
            } else {
                die("<!DOCTYPE html>
<html lang='de'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Falsches Passwort</title>
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
    </style>
</head>
<body>
    <main>
        <div class='awasr'>
            <h2>Falsches Passwort!</h2>
            <p>Bitte überprüfen Sie Ihre Eingaben.</p>
        </div>
    </main>
</body>
</html>");
            }
        }
    }

    die("<!DOCTYPE html>
<html lang='de'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Benutzername nicht gefunden</title>
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
    </style>
</head>
<body>
    <main>
        <div class='awasr'>
            <h2>Benutzername nicht gefunden!</h2>
            <p>Bitte überprüfen Sie Ihre Eingaben.</p>
        </div>
    </main>
</body>
</html>");
} else {
    die("Ungültige Anfrage.");
}
?>
