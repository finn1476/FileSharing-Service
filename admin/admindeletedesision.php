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
            echo "<p class='greena'>File is already disabled.</p>";
        } else {
            // Delete the file
            addToCSV($filename, $decryptedData);
            unlink($fileToDelete);
            echo "<p class='greena'>File has been disabled.</p>";
        }
    } else {
        echo "<p class='error'>Error: The file '$filename' does not exist.</p>";
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
    <meta charset="UTF-8">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <style>
        /* CSS styles for the page */
        :root {
            --primary-color: #005f73;
            --secondary-color: #94d2bd;
            --accent-color: #ee9b00;
            --background-color: #f7f9fb;
            --text-color: #023047;
            --muted-text-color: #8e9aaf;
            --border-color: #d9e2ec;
            --button-color: #56cfe1;
            --button-hover-color: #028090;
            --error-color: #e63946;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        main {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .awasr {
            border: 1px solid var(--border-color);
            padding: 20px;
            background-color: var(--background-color);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .alarm, .error {
            color: var(--error-color);
            background-color: var(--background-color);
            border: 1px solid var(--error-color);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .greena {
            color: var(--text-color);
            background-color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .reda {
            color: var(--text-color);
            background-color: var(--error-color);
            border: 1px solid var(--error-color);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container form {
            display: inline-block;
            margin-right: 10px;
        }

        .button-container button {
            border: none;
            padding: 10px 15px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button-container .green {
            background-color: var(--button-color);
        }

        .button-container .green:hover {
            background-color: var(--button-hover-color);
        }

        .button-container .yellow {
            background-color: var(--accent-color);
        }

        .button-container .yellow:hover {
            background-color: var(--button-hover-color);
        }

        .button-container .red {
            background-color: var(--error-color);
        }

        .button-container .red:hover {
            background-color: var(--button-hover-color);
        }

        .button-container .blue {
            background-color: var(--button-color);
        }

        .button-container .blue:hover {
            background-color: var(--button-hover-color);
        }

        .button-container .purple {
            background-color: #6a0dad;
        }

        .button-container .purple:hover {
            background-color: #4b0082;
        }

        .picture-preview {
            max-width: 100%;
            height: auto;
        }

        .preview-container {
            margin: 20px 0;
        }

        .audio-preview {
            width: 100%;
        }

        footer {
            background-color: var(--primary-color);
            padding: 20px;
            color: white;
            text-align: center;
            border-top: 3px solid var(--secondary-color);
        }

        footer .footer-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        footer .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        footer .footer-links a:hover {
            color: var(--accent-color);
        }

        @media (max-width: 600px) {
            footer .footer-links {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
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
                echo "<p>Preview of the file: <br/><img class='picture-preview' src='download_handler.php?filename=$downloadFilename&key=$password' alt='File Preview'></p>";
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
                    echo "<div class='greena'>File is already disabled.</div>";
                    $fileDisabled = true;
                } else {
                    echo "<div class='reda'>File is not disabled.</div>";
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
                    echo "<p class='error'>Unable to open the ZIP file.</p>";
                }
            }
            ?>

            <div class='button-container'>
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

            <div class='button-container'>
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
                <a class='buttona' href='adminpanel5.php'>Statistiken</a>
            </div>
            <div>
                <a class='buttona' href='adminpanel4.php'>Datei-Typen</a>
            </div>
            <div>
                <a class='buttona' href='adminpanel3.php'>Benutzer-Verwaltung</a>
            </div>
            <div>
                <a class='buttona' href='adminpanel2.php'>Upload-Grenze</a>
            </div>
            <div>
                <a class='buttona' href='admindelete.php'>Löschen</a>
            </div>
        </footer>";
    }
    ?>
</body>
</html>
