<?php
// Turn off error reporting
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];
    $csvFile = '../Uploaded_Files/files.csv';
    $filesDirectory = '../Files/';
    $htpasswdFile = '../User/password/.htpasswd';

    if (file_exists($csvFile)) {
        $csvData = file_get_contents($csvFile);
        $lines = explode(PHP_EOL, $csvData);
        $newLines = [];

        foreach ($lines as $line) {
            $data = explode(',', $line);
            if (isset($data[1]) && trim($data[1]) === $username) {
                // Delete the file from the server
                $filePath = $filesDirectory . trim($data[0]);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            } else {
                $newLines[] = $line;
            }
        }

        // Update the CSV file
        file_put_contents($csvFile, implode(PHP_EOL, $newLines));
    }

    // Remove user from .htpasswd
    if (file_exists($htpasswdFile)) {
        $htpasswdData = file_get_contents($htpasswdFile);
        $lines = explode(PHP_EOL, $htpasswdData);
        $newLines = [];

        foreach ($lines as $line) {
            if (strpos($line, $username . ':') !== 0) {
                $newLines[] = $line;
            }
        }

        // Update the .htpasswd file
        file_put_contents($htpasswdFile, implode(PHP_EOL, $newLines));
    }
}

header('Location: adminpanel3.php');
exit();
?>
