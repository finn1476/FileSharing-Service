<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

// Decrypt file data using AES-256-CBC
function decryptFile($data, $key) {
    $cipherMethod = 'aes-256-cbc';
    $ivLength = openssl_cipher_iv_length($cipherMethod);
    
    // Assuming data is base64 encoded
    $data = base64_decode($data);
    if ($data === false) {
        die("Base64 decoding failed.");
    }

    $iv = substr($data, 0, $ivLength);
    $encryptedData = substr($data, $ivLength);

    if ($iv === false || $encryptedData === false) {
        die("IV or encrypted data extraction failed.");
    }

    // Use the key directly if it’s the correct length for aes-256-cbc
    $decryptedData = openssl_decrypt($encryptedData, $cipherMethod, $key, OPENSSL_RAW_DATA, $iv);
    
    if ($decryptedData === false) {
        die("Decryption failed. Ensure the data is encrypted with the same method and key.");
    }

    return $decryptedData;
}

// Function to check if the hash is already in the CSV file
function isHashInCSV($hashValue, $csvFile) {
    if (!file_exists($csvFile)) {
        return false;
    }
    
    $csvData = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Check if the hash is already in the CSV file
    return in_array($hashValue, $csvData);
}

// Function to add the file hash to the CSV file
function addToCSV($filename, $key) {
    $filePath = '../Files/' . $filename;

    // Check if the file exists
    if (!file_exists($filePath)) {
        echo "Error: The file '$filename' does not exist.";
        return;
    }

    // Read encrypted file data
    $encryptedData = file_get_contents($filePath);

    // Decrypt file data
    $decryptedData = decryptFile($encryptedData, $key);

    if ($decryptedData === false) {
        echo "Error: Decryption failed.";
        return;
    }

    $csvFile = '../Speicher/hashes.csv';
    $hashValue = hash('sha256', $decryptedData);

    // Check if the hash is already in the CSV file
    if (!isHashInCSV($hashValue, $csvFile)) {
        // Save the hash value in the CSV file
        file_put_contents($csvFile, $hashValue . PHP_EOL, FILE_APPEND | LOCK_EX);
        echo "Hash added to CSV.";
    } else {
        echo "Hash is already in the CSV file.";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_REQUEST["filename"]) && isset($_REQUEST["password"])) {
        $downloadFilename = $_REQUEST["filename"];
        $key = $_REQUEST["password"]; // Make sure to sanitize and validate this key

        // Add hash to CSV
        addToCSV($downloadFilename, $key);
    } else {
        echo "Error: Missing filename or password.";
    }
} else {
    echo "Invalid request.";
}
?>
