<!DOCTYPE html>
<html lang="en">

<head>
    <title>Datei herunterladen</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="noindex">
    <link rel="stylesheet" type="text/css" href="style.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>

<body>
    <div class="awasr">
        <?php
        ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

        // Funktion, um die Dateigröße zu formatieren
        function formatSizeUnits($bytes) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
        }

        $downloadFilename = $_GET['filename'] ?? null;
        $downloadPath = 'Files/' . $downloadFilename;
        $absolutePath = realpath($downloadPath);
        $currentDomain = $_SERVER['HTTP_HOST'];
        $disabledFiles = file('disabled_files.txt', FILE_IGNORE_NEW_LINES);
        $csvFileHashes = 'Speicher/hashes.csv'; // CSV file for storing hashes
        $fileuploadcsv = 'Uploaded_Files/uploaded_files.csv';
        $statusFile = 'Uploaded_Files/statusupload.csv';

        if (strpos($absolutePath, realpath('Files')) !== 0) {
            // Pfad ist nicht im erwarteten Verzeichnis
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
                // Fehlerbehandlung: konnte die Datei nicht sperren
                echo "Fehler: Konnte die Datei nach $maxRetries Versuchen nicht sperren.";
            }
        } else {
            echo "Status ist nicht 1. Vorgang abgebrochen.";
        }
        if ($downloadFilename) {
            $downloadPath = 'Files/' . $downloadFilename;

            if (file_exists($downloadPath)) {
                // Check if the hash of the file matches any hash in the hashes.csv file
                $hashValue = hash_file('sha256', $downloadPath);
                $csvDataHashes = file_get_contents($csvFileHashes);

                if (strpos($csvDataHashes, $hashValue) !== false || in_array($downloadFilename, $disabledFiles)) {
                    echo "Download of this file has been disabled.";
                    echo "<a class=\"buttona\" href=\"index.php\">HOME</a>";
                } else {
                    echo "<h1>Download File</h1>";

                    // Get file size
                    $fileSize = filesize($downloadPath);
                    $fileSizeFormatted = formatSizeUnits($fileSize);
                    echo "<p>File Size: $fileSizeFormatted</p>";

                    // Check if it's a ZIP file
                    $fileExtension = pathinfo($downloadFilename, PATHINFO_EXTENSION);
                    if (strtolower($fileExtension) === 'zip') {
                        $zip = new ZipArchive;
                        if ($zip->open($downloadPath) === TRUE) {
                            echo "<h2>Contents of the ZIP file:</h2>";
                            echo "<ul>";
                            $maxEntriesToShow = 15;
                            for ($i = 0; $i < min($zip->numFiles, $maxEntriesToShow); $i++) {
                                $filename = $zip->getNameIndex($i);
                                echo "<li>$filename</li>";
                            }
                            echo "</ul>";

                            $zip->close();
                        } else {
                            echo "Unable to open the ZIP file.";
                        }
                    }

                    // Check if it's an image file
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];
                    $audioExtensions = ['mp3', 'ogg', 'wav']; // Add audio extensions

                    if (in_array(strtolower($fileExtension), $imageExtensions)) {
                        echo "<p>Preview of the file: <br/><img class='picture-preview' src='download_handler.php?filename=$downloadFilename' alt='File Preview' ></p>";
                    } elseif (in_array(strtolower($fileExtension), $videoExtensions)) {
                        echo "<p>Preview of the file:</p>";
                        echo "<div class='preview-container'>";
                        echo "<video class='picture-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='download_handler.php?filename=$downloadFilename' type='video/mp4'></video>";
                        echo "</div>";
                    } elseif (in_array(strtolower($fileExtension), $audioExtensions)) {
                        echo "<p>Preview of the file:</p>";
                        echo "<div class='preview-container'>";
                        echo "<audio class='audio-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='download_handler.php?filename=$downloadFilename' type='audio/mpeg'></audio>";
                        echo "</div>";
                    }

                    echo "<p class='preview-text'>Click on the following button to download the following file: <strong>$downloadFilename</strong></p>";
                    echo "<a href='download_handler.php?filename=$downloadFilename'>";
                    echo "<button>Start the download</button>";
                    echo "</a>";
                    echo "<a class=\"buttona\" href=\"index.php\">HOME</a>";
                }
            } else {
                echo "The requested file does not exist.";
                echo "<a class=\"buttona\" href=\"index.php\">HOME</a>";
            }
        } else {
            echo "No file requested.";
            echo "<a class=\"buttona\" href=\"index.php\">HOME</a>";
        }

        ?>

    </div>
    <footer class="footera">
        <?php include("templates/footer.php"); ?>
    </footer>

    <script>
        function limitPlayTime(mediaElement, maxSeconds) {
            let currentTime = 0;

            mediaElement.addEventListener('timeupdate', function () {
                currentTime = mediaElement.currentTime;

                if (currentTime > maxSeconds) {
                    mediaElement.pause();
                }
            });
        }
    </script>

</body>

</html>
