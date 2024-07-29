<?php
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

// Function to check if the hash is in the CSV file
function isHashInCSV($hashValue, $csvFile) {
    $csvData = file_get_contents($csvFile);
    $hashes = explode(PHP_EOL, $csvData);

    // Check if the hash is in the CSV file
    return in_array($hashValue, $hashes);
}

// Function to remove the file hash from the CSV file
function removeFromCSV($filename) {
    // Implement logic to remove the file hash from the CSV file
    // You may use the $filename to locate and remove the hash
    // Add your logic here

    $csvFile = '../Speicher/hashes.csv';
    $hashValueToRemove = hash_file('sha256', '../Files/' . $filename);

    // Check if the hash is in the CSV file
    if (isHashInCSV($hashValueToRemove, $csvFile)) {
        // Read the current CSV file
        $csvData = file_get_contents($csvFile);

        // Remove the hash from the CSV data
        $csvData = str_replace($hashValueToRemove . PHP_EOL, '', $csvData);

        // Update the CSV file
        file_put_contents($csvFile, $csvData, LOCK_EX);
        // Redirect to the admindeletedesision page
		header("refresh:2;url=admindelete.php");
        exit();
    } else {
        echo "Hash not found in the CSV file.";
        // Redirect to the admindeletedesision page
        header("refresh:2;url=admindelete.php");
        exit();
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $downloadFilename = $_POST["filename"];

    // Remove hash from CSV
    removeFromCSV($downloadFilename);
} else {
    echo "Invalid request.";
}
?>
