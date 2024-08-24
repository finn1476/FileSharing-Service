<?php
session_start();
require 'config.php';

// Define default values if not set
$csvFile = 'Speicher/hashes.csv'; // CSV file for hash values
$csvSettingsFile = 'Speicher/settings.csv'; // CSV file for storing settings
$selectedFiletypesFile = 'Speicher/selected_filetypes.csv'; // CSV file for allowed file types
$tempUploadDir = 'TempFiles/'; // Temporary upload directory
$uploadDir = 'Files/'; // Define the upload directory
$currentDomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''; // Define the current domain

// Ensure temp upload directory exists
if (!is_dir($tempUploadDir)) {
    mkdir($tempUploadDir, 0755, true);
}

// Function to generate a random encryption key
function generateEncryptionKey($length = 32) {
    return bin2hex(random_bytes($length));
}

// Function to encrypt data
function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encryptedData); // Combine IV and encrypted data
}

// Read settings from CSV file
function readSettingsFromCsv($csvFile) {
    $settingsData = array();
    if (($handle = fopen($csvFile, 'r')) !== false) {
        $row = fgetcsv($handle);
        if (!empty($row)) {
            $settingsData['maximumFileSize'] = isset($row[0]) ? intval($row[0]) * 1048576 : 5242880; // Convert MB to Bytes (default: 5 MB)
        }
        fclose($handle);
    }
    return $settingsData;
}

$settingsData = readSettingsFromCsv($csvSettingsFile);
$maximumFileSize = $settingsData['maximumFileSize'] ?? 5242880; // 5 MB (in Bytes)

// Read allowed file types from CSV file
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

$selectedFiletypesData = readSelectedFileTypesFromCsv($selectedFiletypesFile);
$allowedExtensions = isset($selectedFiletypesData[0]) ? $selectedFiletypesData[0] : array();

function generateRandomFileName($fileExt, $length = 16) {
    return bin2hex(random_bytes($length)) . '.' . $fileExt;
}
// Function to get the file upload limit from the database
function getFileUploadLimitFromDb($pdo) {
    $sql = "SELECT upload_limit_file FROM file_upload_limits WHERE id = 1"; // Assuming 1 is the ID for the limit
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn() * 1048576; // Convert MB to Bytes
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

// Function to add file name and username to another CSV file
function addFileNameAndUsernameToCsv($csvFile, $fileName, $username) {
    $fileData = array($fileName, $username);

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

// Function to update the total upload size for a user
function updateUserUploadSize($pdo, $userId, $fileSize) {
    // Begin a transaction
    $pdo->beginTransaction();
    
    try {
        // Check if user already has an entry
        $sql = "SELECT SUM(file_size) AS total_size FROM uploads WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalSize = $result['total_size'] ?? 0;

        // Add the new file size to the total
        $newSize = $totalSize + $fileSize;

        // Check if the new size exceeds the user's upload limit
        $sql = "SELECT upload_limit FROM file_upload_limits 
                JOIN users ON users.file_upload_limit_id = file_upload_limits.id 
                WHERE users.id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $uploadLimitMB = $stmt->fetchColumn();

        // Convert MB to Bytes
        $uploadLimit = $uploadLimitMB * 1048576;

        if ($uploadLimit !== false && $newSize > $uploadLimit) {
            // Log the error
            error_log("Exception: Upload exceeds the user's storage limit.");
            
            throw new Exception("Storage limit exceeded.");
        }

        // Insert the new file entry
        $sql = "INSERT INTO uploads (user_id, file_size) VALUES (:user_id, :file_size)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':file_size' => $fileSize
        ]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        // Log the exception message
        error_log("Exception: " . $e->getMessage());
        throw $e;
    }
}

// Function to get the file upload limit for a specific user from the database
function getUserUploadLimit($pdo, $userId) {
    $sql = "SELECT upload_limit_file 
            FROM file_upload_limits 
            JOIN users ON users.file_upload_limit_id = file_upload_limits.id 
            WHERE users.id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    $uploadLimitMB = $stmt->fetchColumn();
    return $uploadLimitMB ? $uploadLimitMB * 1048576 : 0; // Convert MB to Bytes, return 0 if no limit found
}
// Get the upload limit for non-logged-in users (ID 1)
function getAnonymousUploadLimit($pdo) {
    $sql = "SELECT upload_limit FROM file_upload_limits WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn() * 1048576; // Convert MB to Bytes
}

// Check for file upload errors
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file1"])) {
    $file = $_FILES["file1"];

    // Check for file upload errors
    if ($file["error"] === UPLOAD_ERR_OK) {
        global $pdo;
        // Initialize upload limit
        $uploadLimit = 0;

        // Check if the user is logged in
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['username'])) {
            // Get the user's ID from the session
            $username = $_SESSION['username'];
            $sql = "SELECT id FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userId = $user['id'];

                // Get the user's upload limit
                $uploadLimit = getUserUploadLimit($pdo, $userId);
            } else {
                echo "<div class='alert error'>User not found.</div>";
                exit();
            }
        } else {
            // Handle case for non-logged in users
            $uploadLimit = getAnonymousUploadLimit($pdo);
        }

        if ($file["size"] > $uploadLimit) {
            echo "<div class='alert error'>The file is too large. Please select a file that is not larger than " . ($uploadLimit / 1048576) . " MB.</div>";
            exit();
        }

        // Check file size using the updated $maximumFileSize
        if ($file["size"] <= $maximumFileSize) {
            $fileName = $file["name"];
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileData = file_get_contents($file["tmp_name"]);
            $fileHash = hash('sha256', $fileData);

            // Check if the file extension is in the allowed list from CSV file
            if (in_array(strtolower($fileExt), $allowedExtensions)) {
                // Generate a unique random file name
                $randomName = generateRandomFileName($fileExt);
                $tempDestination = $tempUploadDir . $randomName;
                $destination = $uploadDir . $randomName;

                // Check if the hash value is present in the hashes.csv file
                $hashesData = hashreadfromSCV($csvFile);
                $existingHashes = array_column($hashesData, 0); // Extract all hash values from the CSV file

                // Check if the hash value of the uploaded file is already in the CSV file
                if (in_array($fileHash, $existingHashes)) {
                    // If the hash is found in the CSV, it means the file is disabled
                    echo "<div class='alert warning'>File is disabled.</div>";
                    exit(); // Stop further execution
                }

                // Perform user storage limit check before any file operation
                if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['username'])) {
                    try {
                        // Datenbankverbindung (aus config.php)
                        global $pdo;

                        // Benutzername von der Session holen
                        $username = $_SESSION['username'];

                        // Benutzer-ID abrufen
                        $sql = "SELECT id FROM users WHERE username = :username";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([':username' => $username]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user) {
                            $userId = $user['id'];

                            // Generate a random encryption key
                            $encryptionKey = generateEncryptionKey();

                            // Encrypt the file data
                            $encryptedData = encryptData($fileData, $encryptionKey);

                            // Save the encrypted data to the temporary destination file
                            file_put_contents($tempDestination, $encryptedData);

                            // Get the size of the encrypted file
                            $encryptedFileSize = filesize($tempDestination);

                            // Update user upload size with the size of the encrypted file
                            try {
                                updateUserUploadSize($pdo, $userId, $encryptedFileSize);

                                // Move the file to the final destination
                                rename($tempDestination, $destination);

                                // Add the file name to uploaded_files.csv if statusupload.csv contains "1"
                                $statusUploadFile = 'Uploaded_Files/statusupload.csv';
                                if (($handle = fopen($statusUploadFile, 'r')) !== false) {
                                    $status = fgetcsv($handle)[0]; // Read the first value
                                    fclose($handle);
                                    $filesCsv = 'Uploaded_Files/files.csv';
                                    addFileNameAndUsernameToCsv($filesCsv, $randomName, $username);
                                    if ($status == 1) {
                                        // Add the file name to uploaded_files.csv
                                        $uploadedFilesCsv = 'Uploaded_Files/uploaded_files.csv';
                                        addFileNameToCsv($uploadedFilesCsv, $randomName);
                                    }
                                }

                                // Provide download link
                                $downloadLink = "/download.php?filename=$randomName&key=" . urlencode($encryptionKey);
                                $downloadLink2 = "$currentDomain/download.php?filename=$randomName&key=" . urlencode($encryptionKey);


                               echo "<div class='button-container'>";
echo "<a id='downloadLink' href='$downloadLink' class='button'>Visit the download page</a>";
echo "<button onclick='copyToClipboard()' class='button'>Copy the link</button>";
echo "</div>";
                            } catch (Exception $e) {
                                // Remove the temp file if something went wrong
                                if (file_exists($tempDestination)) {
                                    unlink($tempDestination);
                                }
                                // Provide a user-friendly message in English
                                echo "<div class='alert error'>Your storage limit has been exceeded. Please free up some space or buy some more.</div>";
                                exit();
                            }
                        } else {
                            echo "<div class='alert error'>User not found.</div>";
                        }
                    } catch (PDOException $e) {
                        echo "<div class='alert error'>Database error: " . $e->getMessage() . "</div>";
                    }
                } else {
                    // No active session, use the anonymous upload limit
                    global $pdo;
                    $anonymousUploadLimit = getAnonymousUploadLimit($pdo);

                    // Check if the file size exceeds the anonymous upload limit
                    if ($file["size"] <= $anonymousUploadLimit) {
                        // Generate a random encryption key
                        $encryptionKey = generateEncryptionKey();

                        // Encrypt the file data
                        $encryptedData = encryptData($fileData, $encryptionKey);

                        // Save the encrypted data to the temporary destination file
                        file_put_contents($tempDestination, $encryptedData);

                        // Get the size of the encrypted file
                        $encryptedFileSize = filesize($tempDestination);

                        // Move the file to the final destination
                        rename($tempDestination, $destination);

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

                        // Provide download link
                        $downloadLink = "/download.php?filename=$randomName&key=" . urlencode($encryptionKey);
                        $downloadLink2 = "$currentDomain/download.php?filename=$randomName&key=" . urlencode($encryptionKey);
echo "<div class='button-container'>";
echo "<a id='downloadLink' href='$downloadLink' class='button'>Visit the download page</a>";
echo "<button onclick='copyToClipboard()' class='button'>Copy the link</button>";
echo "</div>";
                    } else {
                        echo "<div class='alert error'>The file is too large. Please select a file that is not larger than " . ($anonymousUploadLimit / 1048576) . " MB.</div>";
                    }
                }
            } else {
                echo "<div class='alert error'>Invalid file format. Allowed formats: " . implode(", ", $allowedExtensions) . "</div>";
            }
        } else {
            echo "<div class='alert error'>The file is too large. Please select a file that is not larger than " . ($maximumFileSize / 1048576) . " MB.</div>";
        }
    } else {
        echo "<div class='alert error'>Error while uploading the file: " . $file["error"] . "</div>";
    }
} else {
    echo "<div class='alert warning'>No file selected for upload.</div>";
}
?>
<?php
// Ensure this part is executed where variables are properly set
$downloadLink = "/download.php?filename=$randomName&key=" . urlencode($encryptionKey);
$downloadLink = htmlspecialchars($downloadLink, ENT_QUOTES, 'UTF-8');
?>

<script>
    // Pass the PHP variable to JavaScript
    var downloadLink = "<?php echo $downloadLink; ?>";

    function copyToClipboard() {
        console.log('Download link:', downloadLink); // Debug line
        if (downloadLink) {
            navigator.clipboard.writeText(downloadLink).then(function() {
                alert("Link copied to clipboard!");
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
            });
        } else {
            console.error('Download link is not defined.');
        }
    }
</script>


<!-- Styling for the alerts and buttons -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        text-align: center;
        
    }
    .alert {
        padding: 15px;
        margin: 10px 0;
        border-radius: 5px;
        font-size: 16px;
    }
    .alert.success {
        background-color: #dff0d8;
        color: #3c763d;
    }
    .alert.error {
        background-color: #f2dede;
        color: #a94442;
    }
    .alert.warning {
        background-color: #fcf8e3;
        color: #8a6d3b;
    }
    .button-container {
        display: flex;
        justify-content: center;
        gap: 10px; /* Abstand zwischen den Buttons */
    }
    .button {
        background-color: #5bc0de;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 10px 2px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    .button:hover {
        background-color: #31b0d5;
    }
</style>


