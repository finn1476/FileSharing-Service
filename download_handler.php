<?php

session_start();

?>
<?php
// Define the encryption key
$key = isset($_GET['key']) ? $_GET['key'] : null;

if ($key === null) {
    die("No encryption key provided.");
}

set_time_limit(0); // Setzt das Zeitlimit auf unbegrenzt

require 'config.php'; // Verwendet die erstellte config.php

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to decrypt data
function decryptFile($data, $key) {
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $data = base64_decode($data);

    if ($data === false) {
        die("Base64 decoding failed.");
    }

    $iv = substr($data, 0, $ivLength);
    $encryptedData = substr($data, $ivLength);

    if ($iv === false || $encryptedData === false) {
        die("IV or encrypted data extraction failed.");
    }

    $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    if ($decryptedData === false) {
        die("Decryption failed. The file could not be decrypted.");
    }

    return $decryptedData;
}

// Function to get download speed based on user status
function getDownloadSpeed($pdo, $username) {
    if (empty($username)) {
        // No user logged in, use default limit
        $defaultLimitId = 1;
    } else {
        // Get user file_upload_limit_id
        $stmt = $pdo->prepare("SELECT file_upload_limit_id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $fileUploadLimitId = $user['file_upload_limit_id'];
        } else {
            die("User not found.");
        }

        $defaultLimitId = $fileUploadLimitId;
    }

    // Get download speed from file_upload_limits
    $stmt = $pdo->prepare("SELECT download_speed FROM file_upload_limits WHERE id = :id");
    $stmt->execute(['id' => $defaultLimitId]);
    $limit = $stmt->fetch(PDO::FETCH_ASSOC);

    return $limit ? $limit['download_speed'] : 5; // Default to 5 KB/s if not found
}

// Check if a filename parameter is provided
if (isset($_GET['filename'])) {
    $filename = basename($_GET['filename']); // Get the file name from URL parameter
    $filePath = 'Files/' . $filename; // Path to the encrypted file
    $csvFileHashes = 'Speicher/hashes.csv'; // CSV file for storing hashes
    $disabledFiles = file('disabled_files.txt', FILE_IGNORE_NEW_LINES); // List of disabled files

    // Determine the download speed
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';
    $downloadSpeedKBps = getDownloadSpeed($pdo, $username);

    // Check if the file exists
    if (file_exists($filePath)) {
        // Read the encrypted file content
        $encryptedData = file_get_contents($filePath);

        // Debug output
        if ($encryptedData === false) {
            die("Failed to read the file.");
        }

        // Decrypt the file content
        $decryptedData = decryptFile($encryptedData, $key);

        if ($decryptedData === false) {
            die("Decryption failed. The file could not be decrypted.");
        }

        // Verify the hash of the decrypted file content
        $hashValue = hash('sha256', $decryptedData);
        $csvDataHashes = file_get_contents($csvFileHashes);

        if (strpos($csvDataHashes, $hashValue) !== false || in_array($filename, $disabledFiles)) {
            die("Download of this file has been disabled.");
        }

        // Set the appropriate headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($decryptedData));

        // Define download speed in KB/s
        $chunkSize = $downloadSpeedKBps * 1024; // Convert speed to bytes
        $decryptedDataLength = strlen($decryptedData);
        $offset = 0;

        // Send the decrypted file content in chunks to limit download speed
        while ($offset < $decryptedDataLength) {
            $chunk = substr($decryptedData, $offset, $chunkSize);
            echo $chunk;
            flush(); // Flush the output buffer to ensure the chunk is sent

            $offset += $chunkSize;
            sleep(1); // Wait 1 second between chunks to control speed
        }
        exit();
    } else {
        die("File not found.");
    }
} else {
    die("No file specified.");
}
?>
