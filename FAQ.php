<?php
// Function to read settings from CSV file
function readSettingsFromCsv($csvFile, $delimiter = ',') {
    $settingsData = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Check if the array has at least one element before trying to access it
    if (!empty($settingsData)) {
        return $delimiter === null ? $settingsData : explode($delimiter, $settingsData[0]);
    } else {
        return null; // Return null if the array is empty
    }
}

// Function to convert bytes to megabytes
function bytesToMegabytes($bytes) {
    return round($bytes / 1048576, 2);
}

// Your CSV files
$allowedFileTypesCsv = 'Speicher/selected_filetypes.csv';
$maxFileSizeCsv = 'Speicher/settings.csv';

// Read settings from CSV files
$allowedFileTypes = readSettingsFromCsv($allowedFileTypesCsv);
$maxFileSize = readSettingsFromCsv($maxFileSizeCsv, null); // Pass null to prevent exploding into an array

// Check if $maxFileSize is an array, and handle it accordingly
if (is_array($maxFileSize)) {
    $maxFileSizeBytes = implode('', $maxFileSize);
    $maxFileSize = bytesToMegabytes($maxFileSizeBytes);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Frequently Asked Questions</title>

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
        
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        max-width: 100%;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      ul {
        list-style: none;
        padding: 0;
      }

      li {
        text-align: left;
        margin-bottom: 20px;
      }

      li h3 {
        color: var(--primary-color);
        font-size: 18px;
        margin-bottom: 5px;
      }

      li p {
        margin: 0;
        color: var(--text-color);
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
<header>
    <div class="logo">Anonfile</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="pricing.php">Pricing</a>
        <a href="User/login.php">Login</a>
    </nav>
</header>

<main>
    <div class="awasr">
        <h2>Frequently Asked Questions (FAQ)</h2>

        <ul>
            <li>
                <h3>Do you have any download restrictions?</h3>
                <p>- Yes, <a href="pricing.php">Pricing</a></p>
            </li>
            <li>
                <h3>How long will I be able to access my files?</h3>
                <p>- We strive to maintain accessibility as long as the service is operational, provided that the terms of use are not violated.</p>
            </li>
            <li>
                <h3>What types of files can I upload?</h3>
                <p>- You can upload files with the following extensions: <?php echo implode(', ', $allowedFileTypes); ?>.</p>
            </li>
            <li>
                <h3>Is there a limit on file size?</h3>
                <p>- Yes, <a href="pricing.php">Pricing</a></p>
            </li>
        </ul>


    </div>
</main>
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
