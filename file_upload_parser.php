<?php
session_start();

// Define default values if not set
$csvFile = 'Speicher/hashes.csv'; // CSV file for hash values
$csvSettingsFile = 'Speicher/settings.csv'; // CSV file for storing settings
$selectedFiletypesFile = 'Speicher/selected_filetypes.csv'; // CSV file for allowed file types
$uploadDir = 'Files/'; // Define the upload directory
$currentDomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''; // Define the current domain

// Function to generate a random encryption key
function generateEncryptionKey($length = 32) {
    return bin2hex(random_bytes($length));
}

// Function to encrypt data
function encryptFile($filePath, $key) {
    $data = file_get_contents($filePath);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encryptedData); // Combine IV and encrypted data
}

// Read settings from CSV file
$settingsData = readSettingsFromCsv($csvSettingsFile);
$maximumFileSize = $settingsData['maximumFileSize'] ?? 5242880; // 5 MB (in Bytes)

// Read allowed file types from CSV file
$selectedFiletypesData = readSelectedFileTypesFromCsv($selectedFiletypesFile);
$allowedExtensions = isset($selectedFiletypesData[0]) ? $selectedFiletypesData[0] : array();

// Function to read settings from CSV file
function readSettingsFromCsv($csvFile) {
    $settingsData = array();
    if (($handle = fopen($csvFile, 'r')) !== false) {
        $row = fgetcsv($handle);
        if (!empty($row)) {
            $settingsData['maximumFileSize'] = $row[0] ?? 5242880; // Default: 5 MB (in Bytes)
        }
        fclose($handle);
    }
    return $settingsData;
}

function generateRandomFileName($fileExt, $length = 16) {
    return bin2hex(random_bytes($length)) . '.' . $fileExt;
}

// Function to read selected file types from CSV file
function readSelectedFileTypesFromCsv($csvFile) {
    $selectedFileTypesData = array();
    if (($handle = fopen($csvFile, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $selectedFileTypesData[] = $row;
        }
        fclose($handle);
    }
    return $selectedFileTypesData;
}

// Function to add file name to CSV file with current date
function addFileNameToCsv($csvFile, $fileName) {
    $date = date("Y-m-d"); // Get current date
    $fileData = array($fileName, $date);

    // Attempt to acquire a lock on the CSV file with 60 tries
    $lockAttempts = 60;
    while ($lockAttempts > 0) {
        $fileHandle = fopen($csvFile, 'a'); // Open CSV file in append mode
        if (flock($fileHandle, LOCK_EX)) { // Exclusive lock
            fputcsv($fileHandle, $fileData); // Write data to CSV
            flock($fileHandle, LOCK_UN); // Release the lock
            fclose($fileHandle); // Close CSV file
            return; // Exit the function
        }
        $lockAttempts--;
        usleep(500000); // Sleep for 0.5 seconds between attempts
    }
}

// Function to write settings to CSV file
function writeSettingsToCsv($csvFile, $settingsData) {
    $file = fopen($csvFile, 'w');
    fputcsv($file, $settingsData);
    fclose($file);
}

// Function to add file name and username to CSV file
function addFileNameAndUsernameToCsv($csvFile, $fileName, $username) {
    $fileData = array($fileName, $username, date("Y-m-d H:i:s")); // Date added
    $fileHandle = fopen($csvFile, 'a');
    fputcsv($fileHandle, $fileData);
    fclose($fileHandle);
}

// Function to read hashes from CSV file
function hashreadfromSCV($csvFile) {
    $settingsData = array();
    if (($handle = fopen($csvFile, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $settingsData[] = $row;
        }
        fclose($handle);
    }

    return $settingsData;
}

// Check for file upload errors
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file1"])) {
    $file = $_FILES["file1"];

    // Check for file upload errors
    if ($file["error"] === UPLOAD_ERR_OK) {
        // Check file size using the updated $maximumFileSize
        if ($file["size"] <= $maximumFileSize) {
            $tempName = $file["tmp_name"];
            $originalName = $file["name"];
            $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);

            // Check if the file extension is in the allowed list from CSV file
            if (in_array(strtolower($fileExt), $allowedExtensions)) {
                $fileHash = hash_file('sha256', $tempName);

                // Generate a unique random file name
                $randomName = uniqid() . uniqid() . uniqid() . $fileHash . uniqid() . uniqid() . uniqid() . generateRandomFileName($fileExt);
                $destination = $uploadDir . $randomName;

                // Check if the hash value is present in the hashes.csv file
                $hashesData = hashreadfromSCV($csvFile);
                $existingHashes = array_column($hashesData, 0); // Extract all hash values from the CSV file

                // Check if the hash value of the uploaded file is already in the CSV file
                if (in_array($fileHash, $existingHashes)) {
                    // If the hash is found in the CSV, it means the file is disabled
                    unlink($tempName);
                    exit(); // Stop further execution
                }

                // Add the file name to uploaded_files.csv if statusupload.csv contains "1"
                $statusUploadFile = 'Uploaded_Files/statusupload.csv';
                if (($handle = fopen($statusUploadFile, 'r')) !== false) {
                    $status = fgetcsv($handle)[0]; // Read the first value
                    fclose($handle);

                    if ($status == 1) {
                        // Add the file name to uploaded_files.csv
                        $uploadedFilesCsv = 'Uploaded_Files/uploaded_files.csv';
                        addFileNameToCsv($uploadedFilesCsv, $randomName);
                    }
                }

                // Generate a random encryption key
                $encryptionKey = generateEncryptionKey();

                // Encrypt the file and save it
                $encryptedData = encryptFile($tempName, $encryptionKey);

                // Save the encrypted data to the destination file
                file_put_contents($destination, $encryptedData);

                // Remove the original file
                unlink($tempName);

                if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['username'])) {
                    // Add the file name and username to the CSV file
                    addFileNameAndUsernameToCsv('Uploaded_Files/files.csv', $randomName, $_SESSION['username']);
                }

                // Provide download link
                $downloadLink = "/download.php?filename=$randomName&key=" . urlencode($encryptionKey);
                $downloadLink2 = "$currentDomain/download.php?filename=$randomName&key=" . urlencode($encryptionKey);

                echo "<span class=\"fasdasd\">The file has been successfully uploaded and renamed to</span><br><br>";
                echo "<center><a id=\"downloadLink\" href=\"$downloadLink\">Visit the download page</a><br></center>";
                echo "<input type=\"hidden\" id=\"downloadLinkText\" value=\"$downloadLink2\"><br>";
                echo "<center><button onclick=\"copyToClipboard()\">Copy the link</button></center>";
            } else {
                echo "Invalid file format. Allowed formats: " . implode(", ", $allowedExtensions);
            }
        } else {
            echo "The file is too large. Please select a file that is not larger than " . ($maximumFileSize / 1048576) . " MB.";
        }
    } else {
        echo "Error while uploading the file: " . $file["error"];
    }
} else {
    echo "No file selected for upload.";
}
?>
