<?php
// Turn off error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Define the encryption key
$key = isset($_POST['password']) ? $_POST['password'] : null; // Adjust according to how you pass the key

// Function to decrypt data
function decryptFile($filePath, $key) {
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $encryptedData = file_get_contents($filePath);

    if ($encryptedData === false) {
        die("Failed to read the file.");
    }

    $data = base64_decode($encryptedData);

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

// Function to check if a file has a preview
function hasPreview($filename) {
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];
    $audioExtensions = ['mp3', 'ogg', 'wav'];
    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

    return (in_array(strtolower($fileExtension), $imageExtensions)
            || in_array(strtolower($fileExtension), $videoExtensions)
            || in_array(strtolower($fileExtension), $audioExtensions));
}

// Function to add the file hash to the CSV file
function addToCSV($filename, $decryptedData) {
    $csvFile = '../Speicher/hashes.csv';
    $hashValue = hash('sha256', $decryptedData);

    // Save the hash value in the CSV file
    $csvData = file_get_contents($csvFile);
    $csvData .= $hashValue . PHP_EOL;
    file_put_contents($csvFile, $csvData, LOCK_EX);
}

// Function to delete the file and add the hash to the CSV file
function deleteFile($filename, $key) {
    $fileToDelete = '../Files/' . $filename;

    if (file_exists($fileToDelete)) {
        $decryptedData = decryptFile($fileToDelete, $key);

        // Check if the hash is already in the CSV file
        $hashValue = hash('sha256', $decryptedData);
        $csvFile = '../Speicher/hashes.csv';

        if (isHashInCSV($hashValue, $csvFile)) {
            echo "File is already disabled.";
        } else {
            // Delete the file
            addToCSV($filename, $decryptedData);
            unlink($fileToDelete);
            echo "File has been disabled.";
        }
    } else {
        echo "<p>Error: The file '$filename' does not exist.</p>";
        echo "<style>.flexidoxi { display: none; }</style>";
    }
}

// Function to check if the hash is in the CSV file
function isHashInCSV($hashValue, $csvFile) {
    $csvData = file_get_contents($csvFile);
    $hashes = explode(PHP_EOL, $csvData);

    return in_array($hashValue, $hashes);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
    <main>
        <div class="awasr">
            <h1>Admin Panel</h1>

            <?php
            $downloadFilename = isset($_POST["filename"]) ? $_POST["filename"] : '';
            $password = isset($_POST["password"]) ? $_POST["password"] : '';
            $downloadPath = '../Files/' . $downloadFilename;
            $fileDisabled = false;
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];
            $audioExtensions = ['mp3', 'ogg', 'wav'];
            $fileExtension = pathinfo($downloadFilename, PATHINFO_EXTENSION);

            if (!file_exists($downloadPath)) {
                echo "<div class='alarm'>FILE NOT FOUND</div>";
            }

            if (in_array(strtolower($fileExtension), $imageExtensions)) {
                echo "<p>Preview of the file: <br/><img class='picture-preview' src='download_handler.php?filename=$downloadFilename&key=$password' alt='File Preview' ></p>";
            } elseif (in_array(strtolower($fileExtension), $videoExtensions)) {
                echo "<p>Preview of the file:</p>";
                echo "<div class='preview-container'>";
                echo "<video class='picture-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='../download_handler.php?filename=$downloadFilename&key=$password' type='video/mp4'></video>";
                echo "</div>";
            } elseif (in_array(strtolower($fileExtension), $audioExtensions)) {
                echo "<p>Preview of the file:</p>";
                echo "<div class='preview-container'>";
                echo "<audio class='audio-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='../download_handler.php?filename=$downloadFilename&key=$password' type='audio/mpeg'></audio>";
                echo "</div>";
            }

            if (file_exists($downloadPath)) {
                $decryptedData = decryptFile($downloadPath, $password);
                $hashValue = hash('sha256', $decryptedData);
                $csvFile = '../Speicher/hashes.csv';

                if (isHashInCSV($hashValue, $csvFile)) {
                    echo "<span class='greena'><center>File is already disabled.</center>";
                    $fileDisabled = true;
                } else {
                    echo "<span class='reda'> <center>File is not disabled.</center>";
                }
            }

            if (in_array(strtolower($fileExtension), ['zip'])) {
                $zip = new ZipArchive;
                if ($zip->open($downloadPath) === TRUE) {
                    echo "<h2>Contents of the ZIP file:</h2>";
                    echo "<ul>";
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        echo "<li>$filename</li>";
                    }
                    echo "</ul>";
                    $zip->close();
                } else {
                    echo "Unable to open the ZIP file.";
                }
            }
            ?>

            <div class='flexidoxi'>
                <?php
                // Display buttons only if the file exists
                if (file_exists($downloadPath)) {
                    ?>
                    <form action="auswertungreport.php" method="post">
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($downloadFilename); ?>">
                        <button type="submit" class="green">OKAY</button>
                    </form>

                    <form action="download_handler.php" method="get">
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($downloadFilename); ?>">
                        <input type="hidden" name="key" value="<?php echo htmlspecialchars($password); ?>">
                        <button type="submit" class="yellow">DOWNLOAD</button>
                    </form>

                    <form action="admindelete_confirm.php" method="post">
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($downloadFilename); ?>">
                        <button type="submit" class="red">LÖSCHEN</button>
                    </form>
                    <?php
                }
                ?>
            </div>
            <div class='flexidoxi'>
                <?php
                // Display buttons only if the file exists and is not disabled
                if (file_exists($downloadPath)){
                    ?>
                    <!-- Add button to add hash to CSV -->
                    <form action="admin_add_hash.php" method="post">
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($downloadFilename); ?>">
                        <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                        <button type="submit" class="blue">ADD HASH TO CSV</button>
                    </form>

                    <!-- Add button to remove hash from CSV -->
                    <form action="admin_remove_hash.php" method="post">
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($downloadFilename); ?>">
                        <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                        <button type="submit" class="purple">REMOVE HASH FROM CSV</button>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        if (!file_exists($downloadPath)) {
            echo "<div class='cdaiwjd'><a class='buttona' href='admindelete.php'>ADMIN</a></div>";
        }
        ?>
    </main>
    <?php
    if (!file_exists($downloadPath)) {
        echo "<footer class='footera'>
        <div>
        <h1 class='right'><a class='bauttona' href='adminpanel5.php'>Statistiken</a></h1>
        </div>
        <div>
        <h1 class='right'><a class='bauttona' href='adminpanel4.php'>Datei-Typen</a></h1>
        </div>
        <div>
        <h1 class='right'><a class='bauttona' href='adminpanel3.php'>Benutzer-Verwaltung</a></h1>
        </div>
        <div>
        <h1 class='right'><a class='bauttona' href='adminpanel2.php'>Upload-Grenze</a></h1>
        </div>
        <div>
        <h1><a class='bauttona' href='admindelete.php'>Löschen</a></h1>
        </div>";
    }
    ?>
    </footer>
</body>
</html>
