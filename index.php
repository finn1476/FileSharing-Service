<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pfad zur CSV-Datei
$csvFilePath = 'Speicher/monero.csv';

// Überprüfen, ob die Datei existiert und lesbar ist
if (file_exists($csvFilePath) && is_readable($csvFilePath)) {
    // Die CSV-Datei öffnen
    $csvFile = fopen($csvFilePath, 'r');

    // Überprüfen, ob das Öffnen erfolgreich war
    if ($csvFile) {
        // Die erste Zeile der CSV-Datei lesen (enthält Option und Adresse)
        $firstLine = fgetcsv($csvFile);

        // Die Monero-Option aus der ersten Zeile extrahieren
        $moneroOption = $firstLine[0];

        // Die Monero-Adresse aus der ersten Zeile extrahieren
        $moneroAddress = $firstLine[1];

        // CSV-Datei schließen
        fclose($csvFile);
    } else {
        // Fehlermeldung, wenn die CSV-Datei nicht geöffnet werden konnte
        echo "Fehler beim Öffnen der CSV-Datei.";
        exit(); // Beende das Skript
    }
} else {
    // Fehlermeldung, wenn die CSV-Datei nicht existiert oder nicht lesbar ist
    echo "Die CSV-Datei existiert nicht oder ist nicht lesbar.";
    exit(); // Beende das Skript
}
?>
<?php
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

// Additional code to display file count and storage space
$filesDir = 'Files/';
if (is_readable($filesDir)) {
    $filesInfo = getFilesInfo($filesDir);
    $fileCount = $filesInfo['fileCount'];
    $totalSize = $filesInfo['totalSize'];
} else {
    echo "Error: Unable to read the /Files directory.";
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

$warn_config_value = function ($ini_name, $var_name, $var_val) {
    $ini_val = ini_get($ini_name);
    if ($ini_val === false) {
        print("<pre>Error retrieving value for $ini_name from php.ini.\n</pre>");
        return;
    }

    $ini_val = intval($ini_val);
    if ($ini_val < $var_val) {
        print("<pre>Warning: php.ini: $ini_name ($ini_val) set lower than $var_name ($var_val)\n</pre>");
    }
};

$uploadMaxFilesize = ini_get('upload_max_filesize');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anonfile</title>
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
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
		padding-left:5rem;
		padding-right:5rem;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .upload-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: var(--background-color);
        padding: 3.5rem;
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        transition: border-color 0.3s ease;
        text-align: center;
      }

      .upload-container:hover {
        border-color: var(--accent-color);
      }

      .progress-wrapper {
        width: 100%;
        margin-bottom: 20px;
      }

      progress {
        width: 100%;
        height: 20px;
        border-radius: 5px;
        overflow: hidden;
        border: none;
        background-color: var(--border-color);
      }

      .progress-text {
        display: flex;
        justify-content: space-between;
        margin-top: 5px;
        font-size: 14px;
        color: var(--muted-text-color);
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
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .custom-file-upload:hover {
        background-color: var(--button-hover-color);
      }

      .upload-icon {
        font-size: 24px;
      }

      #status {
        font-weight: bold;
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

      .donation-section {
        text-align: center;
        margin-top: 40px;
      }

      .donation-section .divider {
        display: inline-block;
        border-bottom: 2px solid var(--border-color);
        width: 80px;
        margin: 0 10px;
      }

      .donation-section span {
        font-size: 18px;
        color: var(--muted-text-color);
        margin: 0 10px;
      }

      .monero-address {
        background-color: var(--secondary-color);
        padding: 10px;
        border-radius: 5px;
        display: inline-block;
        font-family: monospace;
        color: white;
        margin-top: 10px;
      }

      #cookieBanner {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: var(--text-color);
        color: white;
        padding: 15px;
        text-align: center;
        display: none;
        z-index: 9999;
      }

      #cookieBanner button {
        margin-left: 10px;
        padding: 5px 10px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      #cookieBanner button:hover {
        background-color: var(--button-hover-color);
      }

      @media (max-width: 600px) {
        nav {
          flex-direction: column;
        }
      }
      
      input[type="file"] {
        display: none;
      }
    </style>
</head>
<body>
<header>
    <div class="logo">Anonfile</div>
<?php

include("templates/header.php");	
	
?>
</header>

  <main>
    <center><h2>File Upload</h2></center>
    <div class="upload-container">
      <div class="progress-wrapper">
        <progress id="progressBar" value="0" max="100"></progress>
        <div class="progress-text">
          <span id="status">No file selected</span>
          <span id="loaded_n_total"></span>
        </div>
      </div>
      
      <?php
      $datei = fopen("Speicher/filesgrosse.csv", "r");
      $maxsize = fgets($datei, 1000000000);
      fclose($datei);
      if ($totalSize < $maxsize) {
          echo "<label for='file1' class='custom-file-upload'>
                  <i class='upload-icon'>📁</i> Click to select file
                </label>
                <input type='file' name='file1' id='file1' onchange='uploadFile()'>";
				
      } else {
          echo "<p>Upload is currently disabled due to storage limits.</p>";
      }
      ?>
    </div>
  </main>

  <div class="donation-section">
    <?php if ($moneroOption == '1' && $moneroAddress): ?>
      <div class="divider"></div>
      <span>Donate with Monero</span>
      <div class="divider"></div>
      <br>
      <div class="monero-address">
        <?php echo $moneroAddress; ?>
      </div>
    <?php endif; ?>
  </div>

  <div id="cookieBanner">
    <p>This website uses cookies for authentication purposes and to manage the display of this cookie banner. By clicking "Accept", you consent to the use of cookies for these purposes. For more details, please see our <a href="datenschutz.php" style="color: var(--button-color);">Privacy Policy</a>. You can adjust your cookie preferences through your browser settings.</p>
    <button onclick="acceptCookies()">Accept</button>
  </div>

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
  <script>
    function _(el) {
      return document.getElementById(el);
    }

    function uploadFile() {
      var file = _("file1").files[0];
      var formdata = new FormData();
      formdata.append("file1", file);
      var ajax = new XMLHttpRequest();
      ajax.upload.addEventListener("progress", progressHandler, false);
      ajax.addEventListener("load", completeHandler, false);
      ajax.addEventListener("error", errorHandler, false);
      ajax.addEventListener("abort", abortHandler, false);
      ajax.open("POST", "file_upload_parser.php");
      ajax.send(formdata);
    }

    function progressHandler(event) {
    
      var percent = (event.loaded / event.total) * 100;
      _("progressBar").value = Math.round(percent);
      _("status").innerHTML = Math.round(percent) + "% uploaded... please wait";
    }

    function completeHandler(event) {
      _("status").innerHTML = event.target.responseText;
      _("progressBar").value = 0; // will clear progress bar after a successful upload
    }

    function errorHandler(event) {
      _("status").innerHTML = "Upload Failed";
    }

    function abortHandler(event) {
      _("status").innerHTML = "Upload Aborted";
    }

    function showCookieBanner() {
      if (!localStorage.getItem('cookiesAccepted')) {
        document.getElementById('cookieBanner').style.display = 'block';
      }
    }

    function acceptCookies() {
      localStorage.setItem('cookiesAccepted', 'true');
      document.getElementById('cookieBanner').style.display = 'none';
    }
function copyToClipboard() {
  var copyText = document.getElementById("downloadLinkText");
  navigator.clipboard.writeText(copyText.value)
    .then(() => {
      alert('Link copied: ' + copyText.value);
    })
    .catch(err => {
      console.error('Fehler beim Kopieren des Textes: ', err);
    });
}
    // Display the cookie banner
    showCookieBanner();
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
    // 
  </script>
</body>
</html>
