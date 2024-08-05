<?php
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

// Function to get the number of files and total storage space used
function getFilesInfo($dir) {
    $files = glob($dir . '*', GLOB_MARK | GLOB_BRACE);
    $fileCount = 0;
    $totalSize = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileCount++;
            $totalSize += filesize($file);
        }
    }
    
    return array('fileCount' => $fileCount, 'totalSize' => $totalSize);
}

// Function to format bytes into a human-readable format
function formatBytes($bytes, $precision = 2) {
    $units = array("B", "KB", "MB", "GB", "TB");
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . " " . $units[$pow];
}

// Path to the directory containing files
$filesDir = '../Files/';

// Check if directory is readable
if (is_readable($filesDir)) {
    $filesInfo = getFilesInfo($filesDir);
    $fileCount = $filesInfo['fileCount'];
    $totalSize = $filesInfo['totalSize'];


    // Write to CSV file
    $csvFilePath = '../Speicher/file_stats.csv';
    $currentTime = time(); // Current Unix timestamp
    $lastRunTime = file_exists($csvFilePath) ? filemtime($csvFilePath) : 0; // Last modified time of CSV file

    // Check if a day has passed since the last run
    if ($currentTime - $lastRunTime >= 86400) { // 86400 seconds = 1 day
      

        $csvData[] = array(date('Y-m-d'), $fileCount, $totalSize);

        // Write data to CSV file
        $fp = fopen($csvFilePath, 'a'); // Open CSV file for appending
        foreach ($csvData as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        echo "Data has been written to CSV file.";
    } else {
        echo "Data was last written to CSV file less than a day ago.";
    }
} else {
    echo "Error: Unable to read the /Files directory.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
	
</head>
<body>

    <main>
        <div class="awasr">
            <div><h2>Anonymer File Upload</h2><br></div>
            <div class="maske"><img src="../bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
            <h1>Admin Panel</h1>
			<?php

		$uploadMaxFilesize = ini_get('upload_max_filesize');
    $postMaxSize = ini_get('post_max_size');
    $maxInputTime = ini_get('max_input_time');
    $maxExecutionTime = ini_get('max_execution_time');
    echo"<p>Maximale Datei Größe: $uploadMaxFilesize<br>Maximale Datei Größe: $postMaxSize<br>Maximale Eingabe Zeit: $maxInputTime<br>Maximale Ausgabe Zeit: $maxExecutionTime<br></p>";
	    echo "Number of files in /Files: $fileCount<br>";
    echo "Total storage space used: " . formatBytes($totalSize) . "<br>";

?>
            <p><a class="buttona" href="adminpanel3.php">Zurück</a>
            <a class="buttona" href="index.php">HOME</a></p>
			<a class="buttona" href="cordinaten.php">Auswertung</a>
        </div>

    </main>

    <footer class="footera">
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel5.php">Statistiken</a></h1>
        </div>
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel4.php">Datei-Typen</a></h1>
        </div>
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel3.php">Benutzer-Verwaltung</a></h1>
        </div>
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel2.php">Upload-Grenze</a></h1>
        </div>
        <div>
            <h1><a class="bauttona" href="admindelete.php">Löschen</a></h1>
        </div>
    </footer>
</body>
</html>
