<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
	
?>
<header>
    <div class="logo">Anonfile</div>
<?php

include("templates/header.php");	
	
?>
</header>
<?php
// Funktion, um die Dateigröße zu formatieren
function formatSizeUnits($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}

$downloadFilename = $_GET['filename'] ?? null;
$key = $_GET['key'] ?? null;
$downloadPath = 'Files/' . $downloadFilename;
$absolutePath = realpath($downloadPath);
$currentDomain = $_SERVER['HTTP_HOST'];
$disabledFiles = file('disabled_files.txt', FILE_IGNORE_NEW_LINES);
$csvFileHashes = 'Speicher/hashes.csv'; // CSV file for storing hashes
$fileuploadcsv = 'Uploaded_Files/uploaded_files.csv';
$statusFile = 'Uploaded_Files/statusupload.csv';

if (strpos($absolutePath, realpath('Files')) !== 0) {
    die("Unauthorized access");
}

// Dateiname aus der URL erhalten und das aktuelle Datum ermitteln
$filename = basename($downloadFilename);
$currentDate = date("Y-m-d");

// Überprüfen, ob in der Datei /Uploaded_Files/statusupload.csv eine 1 steht
$statusHandle = fopen($statusFile, 'r');
$status = fgetcsv($statusHandle);
fclose($statusHandle);

if ($status[0] == '1') {
    $maxRetries = 60;
    $retryDelay = 1; // in seconds
    $locked = false;

    for ($i = 0; $i < $maxRetries; $i++) {
        $handle = fopen($fileuploadcsv, "r+");
        if ($handle && flock($handle, LOCK_EX)) {
            $locked = true;
            break;
        }
        if ($handle) {
            fclose($handle);
        }
        sleep($retryDelay);
    }

    if ($locked) {
        $found = false;
        $lines = array();

        // Durch die CSV-Datei zeilenweise iterieren
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Überprüfen, ob der Dateiname in der CSV-Datei gefunden wurde
            if ($data[0] == $filename) {
                $found = true;
                // Das Datum auf das aktuelle Datum setzen
                $data[1] = $currentDate;
            }
            $lines[] = $data;
        }

        // Wenn der Dateiname nicht gefunden wurde, füge ihn hinzu
        if (!$found) {
            $lines[] = array($filename, $currentDate);
        }

        // CSV-Datei schließen und neu öffnen zum Schreiben
        fclose($handle);

        // Datei zum Schreiben öffnen und erneut sperren
        $handle = fopen($fileuploadcsv, "w");
        if ($handle && flock($handle, LOCK_EX)) {
            foreach ($lines as $line) {
                fputcsv($handle, $line);
            }
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    } else {
        echo "<p class='error'>Fehler: Konnte die Datei nach $maxRetries Versuchen nicht sperren.</p>";
    }
}

if ($downloadFilename && $key) {
    if (file_exists($downloadPath)) {
        // Check if the hash of the file matches any hash in the hashes.csv file
        $hashValue = hash_file('sha256', $downloadPath);
        $csvDataHashes = file_get_contents($csvFileHashes);

        if (strpos($csvDataHashes, $hashValue) !== false || in_array($downloadFilename, $disabledFiles)) {
            echo "<p class='error'>Download of this file has been disabled.</p>";
            
        } else {
            // Display file information and preview options
            echo "<main>";
            echo "<h2>Download File</h2>";

            // Get file size
            $fileSize = filesize($downloadPath);
            $fileSizeFormatted = formatSizeUnits($fileSize);
            echo "<p>File Size: $fileSizeFormatted</p>";

            // Check if it's a ZIP file
            $fileExtension = pathinfo($downloadFilename, PATHINFO_EXTENSION);
            if (strtolower($fileExtension) === 'zip') {
                $decryptedTempFile = 'Files/temp_' . $downloadFilename;

                // Retrieve the decrypted ZIP file content using download_handler.php
                $decryptedContent = file_get_contents("http://127.0.0.1/preview_file.php?filename=$downloadFilename&key=$key");

                if ($decryptedContent === false) {
                    echo "<p class='error'>Failed to retrieve decrypted content.</p>";
                } else {
                    // Save the decrypted content to a temporary file
                    file_put_contents($decryptedTempFile, $decryptedContent);

                    $zip = new ZipArchive;
                    if ($zip->open($decryptedTempFile) === TRUE) {
                        echo "<h3>Contents of the ZIP file:</h3>";
                        echo "<ul>";
                        $maxEntriesToShow = 15;
                        for ($i = 0; $i < min($zip->numFiles, $maxEntriesToShow); $i++) {
                            $filename = $zip->getNameIndex($i);
                            echo "<li>$filename</li>";
                        }
                        echo "</ul>";
                        $zip->close();
                    } else {
                        echo "<p class='error'>Unable to open the ZIP file. Error code: " . $zip->open($decryptedTempFile) . "</p>";
                    }

                    // Clean up: remove the temporary decrypted file after processing
                    unlink($decryptedTempFile);
                }
            }

            // Check if it's an image file
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];
            $audioExtensions = ['mp3', 'ogg', 'wav'];

            if (in_array(strtolower($fileExtension), $imageExtensions)) {
                echo "<p>Preview of the file: <br/><img class='picture-preview' src='preview_file.php?filename=$downloadFilename&key=$key' alt='File Preview'></p>";
            } elseif (in_array(strtolower($fileExtension), $videoExtensions)) {
                echo "<p>Preview of the file:</p>";
                echo "<div class='preview-container'>";
                echo "<video class='picture-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='preview_file.php?filename=$downloadFilename&key=$key' type='video/mp4'></video>";
                echo "</div>";
            } elseif (in_array(strtolower($fileExtension), $audioExtensions)) {
                echo "<p>Preview of the file:</p>";
                echo "<div class='preview-container'>";
                echo "<audio class='audio-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='preview_file.php?filename=$downloadFilename&key=$key' type='audio/mpeg'></audio>";
                echo "</div>";
            }

            echo "<p class='preview-text'>Click on the following button to download the following file: <strong>$downloadFilename</strong></p>";
            echo "<a href='download_handler.php?filename=$downloadFilename&key=$key'>";
            echo "<button class='custom-file-upload'>Start the download</button>";
            echo "</a>";
           
            echo "</main>";
        }
    } else {
        echo "<p class='error'>The requested file does not exist.</p>";
        echo "<a class='buttona' href='index.php'>HOME</a>";
    }
} else {
    echo "<p class='error'>No file requested or no key provided.</p>";
    echo "<a class='buttona' href='index.php'>HOME</a>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download File</title>
    <style>
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
      }

      header, footer {
        background-color: var(--primary-color);
        color: white;
        padding: 10px 0;
        text-align: center;
      }

      nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: center;
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

      header {
        background-color: var(--primary-color);
        padding: 10px 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid var(--secondary-color);
      }
      nav ul li {
        display: inline;
        margin: 0 15px;
      }

      nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
      }

      nav ul li a:hover {
        text-decoration: underline;
      }

      footer {
        font-size: 14px;
      }

      main {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        border-radius: 8px;
        background-color: #ffffff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      h2 {
        color: var(--primary-color);
      }

      p.error {
        color: var(--error-color);
        font-weight: bold;
      }

      a.buttona {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 10px;
        text-decoration: none;
        color: #ffffff;
        background-color: var(--button-color);
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
        border: 1px solid transparent;
        transition: background-color 0.3s;
      }

      a.buttona:hover {
        background-color: var(--button-hover-color);
      }

      .custom-file-upload {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 10px;
        background-color: var(--button-color);
        border: none;
        color: #ffffff;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
      }

      .custom-file-upload:hover {
        background-color: var(--button-hover-color);
      }

      .picture-preview {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 10px 0;
      }

      .audio-preview {
        max-width: 100%;
        margin: 10px 0;
      }

      .preview-container {
        max-width: 100%;
        margin: 10px 0;
        overflow: hidden;
      }
	        header {
        background-color: var(--primary-color);
        padding: 10px 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid var(--secondary-color);
      }

      header .logo {
        font-size: 24px;
        font-weight: bold;
      }

      nav {
        display: flex;
        gap: 20px;
      }

      nav a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        font-weight: 500;
      }

      nav a:hover {
        color: var(--accent-color);
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
    </style>
</head>
<body>


  <footer class="footer">
    <div class="footer-links">
      <a href="FAQ.php">FAQ</a>
      <a href="impressum.php">Imprint</a>
     <a href="abuse.php">Abuse</a>
	 <a href="terms.php">ToS</a>
      <a href="datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
  </footer>
</body>
</html>
