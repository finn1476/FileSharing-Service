<?php
// Einbindung der config.php, um die Datenbankverbindung herzustellen
require_once 'config.php';

// URL-Shortening-Funktion
function shortenUrl($url, $pdo) {
    $shortCode = substr(md5(uniqid(rand(), true)), 0, 6);
    $stmt = $pdo->prepare("INSERT INTO urls (short_code, original_url) VALUES (:short_code, :original_url)");
    $stmt->execute(['short_code' => $shortCode, 'original_url' => $url]);
    return $shortCode;
}

// Wenn das Formular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalUrl = filter_var($_POST['url'], FILTER_SANITIZE_URL);
    if (filter_var($originalUrl, FILTER_VALIDATE_URL)) {
        $shortCode = shortenUrl($originalUrl, $pdo);

        // Protokoll und Hostname dynamisch ermitteln
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];

        $shortenedUrl = $protocol . $host . "/url_shortener/" . $shortCode;
    } else {
        $error = "Ungültige URL.";
    }
}
?>
