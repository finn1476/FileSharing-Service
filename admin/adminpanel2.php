
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
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
        max-width: 800px;
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

      .awasr h1 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .awasr h2 {
        color: var(--primary-color);
        font-weight: 500;
        margin-top: 0;
      }

      .awasr label {
        font-weight: bold;
        margin-top: 10px;
        display: block;
      }

      .awasr input[type="number"],
      .awasr input[type="checkbox"] {
        margin-top: 5px;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 16px;
      }

      .awasr p {
        margin-top: 20px;
        font-size: 16px;
      }

      .awasr input[type="submit"] {
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
      }

      .awasr input[type="submit"]:hover {
        background-color: var(--button-hover-color);
      }

      .buttona {
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        display: inline-block;
        margin-right: 10px;
      }

      .buttona:hover {
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
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
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

      .maske img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
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
    </style>
</head>
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
<body>
    <main>
        <div class="awasr">
            <div><h2>Anonymer File Upload</h2><br></div>
            
            <h1>Admin Panel</h1>

            <!-- Add a form to set maximum file size, allowed file types, and a checkbox -->
            <p>
                <form action="adminpanel2.php" method="POST">
                    <label>Maximale Größe von /Files (in MB):</label>
                    <input type="number" name="grosse" value="<?php echo $maximalegrossefuerfiles / 1048576 ?>" min="1" max="100" required />
                    <p>Current Maximum size for /Files: <?php echo $maximalegrossefuerfiles / 1048576 ?> MB</p>
                    <input type="submit" name="updategrose" value="Festlegen">
                </form>
            </p>

            <p>
                <form action="adminpanel2.php" method="POST">
                    <label for="checkboxStatus">Disable / Enable report form:</label>
                    <input type="checkbox" id="checkboxStatus" name="checkboxStatus" value="1" <?php echo $reportStatusFromFile['checkboxStatus'] == 1 ? 'checked' : ''; ?>>
                    <input type="submit" name="updateStatus" value="Aktivieren/Deaktivieren">
                </form>
            </p>

            <p>
                <a class="buttona" href="index.php">Zurück</a>
                <a class="buttona" href="adminpanel3.php">Nächste Seite</a>
            </p>
            <a class="buttona" href="index.php">HOME</a>
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
