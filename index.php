<!DOCTYPE html>

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


<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anonfile</title>
    <link rel="stylesheet" href="style.css">
    <style>
      /* Cookie Banner Styles */
      #cookieBanner {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #333;
        color: white;
        padding: 15px;
        text-align: center;
        display: none; /* Hidden by default */
        z-index: 9999; /* Ensure it is above other elements */
      }
      #cookieBanner button {
        margin-left: 10px;
        padding: 5px 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }
      #cookieBanner button:hover {
        background-color: #45a049;
      }
    </style>
  </head>
  <body>
  <main>
  <div class="awasr">
  <div ><h2>Anonymous File Upload</h2><br></div>
  <div class="maske"><img src="bilder\vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
  <div>
<div class="upload-container">
<progress id="progressBar" value="0" max="100" style="width: 300px;"></progress>
<?php
	$datei = fopen("Speicher/filesgrosse.csv","r");
	$maxsize = fgets($datei, 1000000000);
	fclose($datei);
	if ($totalSize < $maxsize)
	{
		echo "<label for='file1' class='custom-file-upload'>File-Upload</label>
    <input type='file' name='file1' id='file1' onchange='uploadFile()'>";
	}else{
		echo "Upload is currently disabled";
	}

?>


  <h3 id="status"></h3>
  <p id="loaded_n_total"></p>
</div>
</form>
</div>
</div>

</main>

<!-- Donation Button with Monero Wallet Address -->
<div style="text-align: center; margin-top: 20px;">
      <?php if ($moneroOption == '1' && $moneroAddress): ?>
        <div style="display: inline-block; border-bottom: 2px solid #ccc; width: 100px;"></div>
        <span style="font-size: 18px; color: #666; margin: 0 10px;">Donate with Monero</span>
        <div style="display: inline-block; border-bottom: 2px solid #ccc; width: 100px;"></div>
        <br>
        <div style="background-color: #575454; padding: 5px; border-radius: 5px; display: inline-block;">
            <span style="font-size: 14px; font-family: monospace; color: white;">
                <?php echo $moneroAddress; ?>
            </span>
        </div>
    <?php endif; ?>
</div>
<!-- End Donation Button with Monero Wallet Address -->

<!-- Start Cookie Banner -->
<div id="cookieBanner">
  <p>This website uses cookies for authentication purposes and to manage the display of this cookie banner. By clicking "Accept", you consent to the use of cookies for these purposes. For more details, please see our <a href="datenschutz.php" style="color: #4CAF50;">Privacy Policy</a>. You can adjust your cookie preferences through your browser settings.</p>
  <button onclick="acceptCookies()">Accept</button>
</div>
<!-- End Cookie Banner -->

<script>
function _(el) {
  return document.getElementById(el);
}

function uploadFile() {
  var file = _("file1").files[0];
  // alert(file.name+" | "+file.size+" | "+file.type);
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
  _("loaded_n_total").innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
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

// Aufrufen der Funktion zum Anzeigen des Cookie-Banners
showCookieBanner();
</script>
<footer>
<?php include("templates/footer.php"); ?>
</footer>
<script>
// Funktion zum Kopieren des Links in die Zwischenablage
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
</script>

  </body>
</html>
