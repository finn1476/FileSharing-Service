<?php

// Define default values if not set
$csvFile = '../Speicher/hashes.csv'; // CSV file for hash values
$uploadDir = '../Files/'; // Define the upload directory
$currentDomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''; // Define the current domain
$csvSettingsFile = '../Speicher/settings.csv'; // CSV file for storing settings

// Initialize $allowedExtensions if it's not set
$allowedExtensions = isset($_SESSION['allowedExtensions']) ? $_SESSION['allowedExtensions'] : array();

// Additional code for handling settings update
if (isset($_POST['updateSettings'])) {
    $newMaxFileSize = $_POST['maxFileSize'] * 1048576; // Convert MB to Bytes

    // Update the settings
    $_SESSION['maximumFileSize'] = $newMaxFileSize;

    // Write settings to CSV file
    $settingsData = array(
        'maximumFileSize' => $newMaxFileSize,
    );
    writeSettingsToCsv($csvSettingsFile, $settingsData);

    // Update local variables
    $maximumFileSize = $newMaxFileSize;

}

// Function to write settings to CSV file
function writeSettingsToCsv($csvFile, $settingsData) {
    $file = fopen($csvFile, 'w');
    fputcsv($file, $settingsData);
    fclose($file);
}

// Löschen Sie die Datei aus dem "Files"-Verzeichnis
if (isset($_POST['delete'])) {
    $filename = $_POST['filename'];
    $fileToDelete = '../Files/' . $filename;

    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        echo "File deleted successfully.";
    } else {
        // Handle the case where the file doesn't exist or couldn't be deleted
        echo "File not found or could not be deleted.";
    }
}

// Deaktivieren Sie das Herunterladen einer Datei
if (isset($_POST['disable'])) {
    $filename = $_POST['filename'];
    $hashValue = hash_file('sha256', '../Files/' . $filename);

    // Speichern Sie den Hash-Wert in der CSV-Datei
    $csvData = file_get_contents($csvFile);
    $csvData .= $hashValue . PHP_EOL; // Nur den Hash-Wert hinzufügen, nicht den Dateinamen
    file_put_contents($csvFile, $csvData, LOCK_EX);
}

// Aktivieren Sie das Herunterladen einer Datei
if (isset($_POST['enable'])) {
    $filename = $_POST['filename'];

    // Löschen Sie den Hash-Wert aus der Liste
    $hashValue = hash_file('sha256', '../Files/' . $filename);
    $contents = file_get_contents($csvFile);
    $contents = str_replace($hashValue . PHP_EOL, '', $contents);
    file_put_contents($csvFile, $contents);

    // Löschen Sie den Eintrag aus der CSV-Datei
    $csvData = file_get_contents($csvFile);
    $csvData = str_replace($hashValue . PHP_EOL, '', $csvData); // Nur den Hash-Wert entfernen
    file_put_contents($csvFile, $csvData, LOCK_EX);
}

if (isset($_POST['updateFromCSV'])) {
    $csvFile = $_FILES['csvFile']['tmp_name'];

    // Read the existing hashes from the hashes.csv file
    $existingHashes = file('../Speicher/hashes.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Read the new block list from the uploaded CSV file
    $blockList = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Filter out duplicates from the new block list
    $uniqueHashes = array_diff($blockList, $existingHashes);

    // Append only unique hashes to the hashes.csv file
    $hashesCsvFile = '../Speicher/hashes.csv';
    file_put_contents($hashesCsvFile, implode(PHP_EOL, $uniqueHashes) . PHP_EOL, FILE_APPEND | LOCK_EX);
}
function updatePassword($username, $newPassword) {
    $htpasswdFile = '.htpasswd';

    // Generate the password hash using BCRYPT
    $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

    // Read the current .htpasswd file
    $htpasswdData = file($htpasswdFile, FILE_IGNORE_NEW_LINES);

    // Update the password for the specified user
    foreach ($htpasswdData as $key => $line) {
        list($existingUser, $hash) = explode(':', $line, 2);
        if ($existingUser === $username) {
            $htpasswdData[$key] = "$username:$passwordHash";
            break;
        }
    }

    // Update .htpasswd file
    file_put_contents($htpasswdFile, implode("\n", $htpasswdData) . "\n", LOCK_EX);
}

// Additional code for handling password update
if (isset($_POST['updatePassword'])) {
    $username = $_POST['username'];
    $newPassword = $_POST['newPassword1'];

    // Update the password for the specified user
    updatePassword($username, $newPassword);

    echo "Password updated successfully for user: $username.";
}
function addUser($username, $password) {
    $htpasswdFile = '.htpasswd';
    
    // Generate the password hash using BCRYPT
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Append the new user to .htpasswd file
    $htpasswdData = "$username:$passwordHash\n";
    file_put_contents($htpasswdFile, $htpasswdData, FILE_APPEND | LOCK_EX);
}

// Function to delete a user from .htpasswd file
function deleteUser($username) {
    $htpasswdFile = '.htpasswd';
    
    // Read the current .htpasswd file
    $htpasswdData = file($htpasswdFile, FILE_IGNORE_NEW_LINES);
    
    // Remove the user from the array
    foreach ($htpasswdData as $key => $line) {
        list($existingUser, $hash) = explode(':', $line, 2);
        if ($existingUser === $username) {
            unset($htpasswdData[$key]);
            break;
        }
    }
    
    // Update .htpasswd file
    file_put_contents($htpasswdFile, implode("\n", $htpasswdData) . "\n", LOCK_EX);
}

// Additional code for handling user actions
if (isset($_POST['addUser'])) {
    $newUsername = $_POST['newUsername'];
    $newPassword = $_POST['newPassword'];
    
    addUser($newUsername, $newPassword);
    
    echo "User added successfully.";
    echo $newUsername;
}

if (isset($_POST['deleteUser'])) {
    $usernameToDelete = $_POST['usernameToDelete'];
    
    deleteUser($usernameToDelete);
    
    echo "User deleted successfully.";
}

// Automatische Weiterleitung nach 2 Sekunden auf adminpanel.php
header("refresh:2;url=index.php");


// Function to write report status to CSV file
function writeReportStatusToCsv($csvFile, $reportStatusData) {
    $file = fopen($csvFile, 'w');
    fputcsv($file, $reportStatusData);
    fclose($file);
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Please Wait</title>
</head>
<h1>Bitte warte bis du weitergeleitet wirst!</h1>
</html>
