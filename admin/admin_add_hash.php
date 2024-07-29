<?php
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

// Function to check if the hash is already in the CSV file
function isHashInCSV($hashValue, $csvFile) {
    $csvData = file_get_contents($csvFile);
    $hashes = explode(PHP_EOL, $csvData);

    // Check if the hash is already in the CSV file
    return in_array($hashValue, $hashes);
}

// Function to add the file hash to the CSV file
function addToCSV($filename) {
    // Implement logic to add the file hash to the CSV file
    // You may use the $filename to get the hash and other details
    // Add your logic here

    $csvFile = '../Speicher/hashes.csv';
    $hashValue = hash_file('sha256', '../Files/' . $filename);

    // Check if the hash is already in the CSV file
    if (!isHashInCSV($hashValue, $csvFile)) {
        // Save the hash value in the CSV file
        $csvData = file_get_contents($csvFile);
        $csvData .= $hashValue . PHP_EOL; // Add only the hash value, not the filename
        file_put_contents($csvFile, $csvData, LOCK_EX);
        // Redirect to the admindeletedesision page
		header("refresh:2;url=admindelete.php");
        exit();
    } else {
        echo "Hash is already in the CSV file.";
        // Redirect to the admindeletedesision page
        header("refresh:2;url=admindelete.php");
        exit();
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $downloadFilename = $_POST["filename"];

    // Add hash to CSV
    addToCSV($downloadFilename);
} else {
    echo "Invalid request.";
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $downloadFilename = $_GET['filename'];

    // Add hash to CSV
    addToCSV($downloadFilename);
} else {
    echo "Invalid request.";
}
?>
