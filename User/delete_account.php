<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

function deleteUserFromHtpasswd($username) {
    $htpasswdFile = __DIR__ . '/password/.htpasswd'; // Pfad zur .htpasswd-Datei
    if (file_exists($htpasswdFile)) {
        $htpasswd = file($htpasswdFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $found = false;
        foreach ($htpasswd as $key => $line) {
            $parts = explode(':', $line);
            if ($parts[0] === $username) {
                unset($htpasswd[$key]);
                $found = true;
                break;
            }
        }
        if ($found) {
            file_put_contents($htpasswdFile, implode("\n", $htpasswd) . "\n"); // Füge einen Absatz hinzu
            echo "Der Benutzer '$username' wurde aus .htpasswd gelöscht.";
        } else {
            echo "Der Benutzer '$username' wurde nicht in .htpasswd gefunden.";
        }
    } else {
        echo "Die .htpasswd-Datei wurde nicht gefunden.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_account'])) {
        if (!isset($_SESSION['username'])) {
            echo "Session nicht initialisiert oder Benutzer nicht angemeldet.";
            exit;
        }
        $loggedInUser = $_SESSION['username'];
        if (isset($_POST['username']) && $_POST['username'] === $loggedInUser) {
            deleteUserFromHtpasswd($loggedInUser);
            session_unset();
            session_destroy();
            header("Location: ../index.php");
            exit;
        } else {
            echo "Benutzername stimmt nicht überein.";
            exit;
        }
    } else {
        echo "'delete_account' Feld nicht gesetzt.";
        exit;
    }
} else {
    echo "Ungültige Anfrage: Keine POST-Anfrage.";
    exit;
}
?>
