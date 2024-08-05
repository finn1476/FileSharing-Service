<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];
$uploadDir = 'Files/';
$csvSettingsFile = '../Speicher/settings.csv'; // CSV file for storing maximum file size
$csvReportStatusFile = '../Speicher/reportstatus.csv'; // CSV file for storing checkbox status
$csvfilesgrosse = '../Speicher/filesgrosse.csv';

// Function to read settings from CSV file
function readSettingsFromCsv($csvFile) {
    $settingsData = ['maximumFileSize' => null, 'checkboxStatus' => null]; // Set default values

    if (($file = fopen($csvFile, 'r')) !== FALSE) {
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            if (count($data) > 0) {
                $settingsData['maximumFileSize'] = isset($data[0]) ? $data[0] : null;
                $settingsData['checkboxStatus'] = isset($data[1]) ? $data[1] : null;
            }
        }
        fclose($file);
    }

    return $settingsData;
}

// Function to write settings to CSV file
function writeSettingsToCsv($csvFile, $settingsData) {
    $file = fopen($csvFile, 'w');
    fputcsv($file, $settingsData);
    fclose($file);
}

// Function to read report status from CSV file
function readReportStatusFromCsv($csvFile) {
    $reportStatusData = ['checkboxStatus' => 0]; // Set default value

    if (($file = fopen($csvFile, 'r')) !== FALSE) {
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            if (count($data) > 0) {
                $reportStatusData['checkboxStatus'] = isset($data[0]) ? $data[0] : 0;
            }
        }
        fclose($file);
    }
    return $reportStatusData;
}

// Function to write report status to CSV file
function writeReportStatusToCsv($csvFile, $reportStatusData) {
    $file = fopen($csvFile, 'w');
    fputcsv($file, $reportStatusData);
    fclose($file);
}

// Read settings from CSV file
$settingsFromFile = readSettingsFromCsv($csvSettingsFile);
$reportStatusFromFile = readReportStatusFromCsv($csvReportStatusFile);

// Set default values if not present in the CSV file
$maximumFileSize = isset($settingsFromFile['maximumFileSize']) ? $settingsFromFile['maximumFileSize'] : 10485760; // Default: 10 MB

// Additional code for handling settings update
if (isset($_POST['updateSettings'])) {
    $newMaxFileSize = $_POST['maxFileSize'] * 1048576; // Convert MB to Bytes

    // Update the settings
    $settingsData = array(
        'maximumFileSize' => $newMaxFileSize,
    );
    writeSettingsToCsv($csvSettingsFile, $settingsData);

    // Update local variables
    $maximumFileSize = $newMaxFileSize;
}

if (isset($_POST['updateStatus'])) {
    $reportStatusData = array(
        'checkboxStatus' => isset($_POST['checkboxStatus']) ? intval($_POST['checkboxStatus']) : 0,
    );
    writeReportStatusToCsv($csvReportStatusFile, $reportStatusData);
}
if (isset($_POST['updategrose'])) {
    $reportStatusData = array(
        'grosse' => isset($_POST['grosse']) ? intval($_POST['grosse']) : 0,
    );
	$asidjaw = $_POST['grosse'] * 1048576;
    $file = fopen($csvfilesgrosse, 'w');
    fputcsv($file, [$asidjaw]);
    fclose($file);
}
$fiasdle = fopen($csvfilesgrosse, 'r');
$maximalegrossefuerfiles = fread($fiasdle, filesize($csvfilesgrosse));
$maximalegrossefuerfiles = intval($maximalegrossefuerfiles);
fclose($fiasdle);
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

            <!-- Add a form to set maximum file size, allowed file types, and a checkbox -->
            <form action="admin.php" method="post">
                <label for="maxFileSize">Max File Size (in MB):</label><br>
                <input type="number" id="maxFileSize" name="maxFileSize" value="<?php echo $settingsFromFile['maximumFileSize'] / 1048576; ?>"><br>

                <!-- Display current settings -->
                <p>Current Maximum File Size: <?php echo $settingsFromFile['maximumFileSize'] / 1048576; ?> MB</p>

                <!-- Add checkbox for status -->
                

                
                <input type="submit" name="updateSettings" value="Update Settings">
            </form>
			<p>
			<form  action="adminpanel2.php" method="POST" name="">
			<label>Maximale Größe von /Files</label><br>
			<input type="number" name="grosse" value="<?php echo $maximalegrossefuerfiles / 1048576?>" min="" max="" size="" maxlength="" />
			<p>Current Maximum size for /Files: <?php echo $maximalegrossefuerfiles / 1048576?> MB</p>
			<input type="submit" name="updategrose" value="Festlegen">
			</form>
			</p>
			
<form  action="adminpanel2.php" method="POST" name="">
<label for="checkboxStatus">Disable / enabel report form:</label>
                <input type="checkbox" id="checkboxStatus" name="checkboxStatus" value="1" <?php echo $reportStatusFromFile['checkboxStatus'] == 1 ? 'checked' : ''; ?>>
			 <input type="submit" name="updateStatus" value="Aktivieren/Deaktivieren">
</form>
            <p><a class="buttona" href="index.php">Zurück</a>
            <a class="buttona" href="adminpanel3.php">Nächste Seite</a></p>
            <a class="buttona" href="index.php">HOME</a></p>
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
