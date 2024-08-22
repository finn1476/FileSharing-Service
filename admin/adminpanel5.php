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

// Initialize file information
$fileCount = 0;
$totalSize = 0;

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

     
    } else {
        
    }
} else {
    echo "Error: Unable to read the /Files directory.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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

      main {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        background-color: var(--background-color);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .awasr p {
        margin: 0 0 15px 0;
      }

      .impressum {
        margin-top: 30px;
      }

      .impressum label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }

      .impressum input,
      .impressum textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        box-sizing: border-box;
        margin-bottom: 15px;
        font-size: 16px;
      }

      .impressum textarea {
        height: 150px;
        resize: vertical;
      }

      .custom-file-upload {
        cursor: pointer;
        background-color: var(--button-color);
        color: white;
        padding: 15px 30px;
        border-radius: 5px;
        border: none;
        font-size: 18px;
        transition: background-color 0.3s ease;
        display: inline-block;
        text-align: center;
        text-decoration: none;
      }

      .custom-file-upload:hover {
        background-color: var(--button-hover-color);
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
        nav {
          flex-direction: column;
          gap: 10px;
        }

        .footer-links {
          flex-direction: column;
          gap: 10px;
        }
      }

      input[type="file"] {
        display: none;
      }
    </style>
</head>
<body>
    <header>
        <div class="logo">Admin Panel</div>
        <nav>
            <a href="adminpanel5.php">Statistiken</a>
            <a href="adminpanel4.php">Datei-Typen</a>
            <a href="adminpanel3.php">Benutzer-Verwaltung</a>
            <a href="adminpanel2.php">Upload-Grenze</a>
            <a href="admindelete.php">Löschen</a>
        </nav>
    </header>

    <main>
        <div class="awasr">
            <h2>Admin Panel</h2>
            <?php
                $uploadMaxFilesize = ini_get('upload_max_filesize');
                $postMaxSize = ini_get('post_max_size');
                $maxInputTime = ini_get('max_input_time');
                $maxExecutionTime = ini_get('max_execution_time');
                echo "<p>Maximale Datei Größe: $uploadMaxFilesize<br>Maximale Datei Größe: $postMaxSize<br>Maximale Eingabe Zeit: $maxInputTime<br>Maximale Ausgabe Zeit: $maxExecutionTime<br></p>";
                echo "Number of files in /Files: $fileCount<br>";
                echo "Total storage space used: " . formatBytes($totalSize) . "<br>";
            ?>
            <p>

                <a class="custom-file-upload" href="cordinaten.php">Auswertung</a>
            </p>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-links">
            <a href="index.php">Linkpage</a>
            <a href="../index.php">Home</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
</body>
</html>
