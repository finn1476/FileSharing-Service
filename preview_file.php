<?php
session_start();



// Define the encryption key
$key = isset($_GET['key']) ? $_GET['key'] : null;

if ($key === null) {
    die("No encryption key provided.");
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

// Check if a filename parameter is provided
if (isset($_GET['filename'])) {
    $filename = basename($_GET['filename']); // Get the file name from URL parameter
    $filePath = 'Files/' . $filename; // Path to the encrypted file
    $csvFileHashes = 'Speicher/hashes.csv'; // CSV file for storing hashes
    $disabledFiles = file('disabled_files.txt', FILE_IGNORE_NEW_LINES); // List of disabled files

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

        // Output the decrypted file content
        echo $decryptedData;
        exit();
    } else {
        die("File not found.");
    }
} else {
    die("No file specified.");
}
?>
