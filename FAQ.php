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
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        main {
            text-align: center;
        }

        .awasr {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 600px;
            width: 100%;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            text-align: left;
            margin-bottom: 10px;
        }

        h2 {
            margin-bottom: 20px;
        }
        
    </style>
</head>
<body>
<main>
    <div class="awasr">
        <div>
            <h2>Frequently Asked Questions (FAQ)</h2><br>
        </div>
        <div class="maske">
            <img src="bilder\vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/>
        </div>

        <ul>
            <li>
                <h3>Do you have any download restrictions?</h3>
                <p>- We do not have any bandwidth limitations in place.</p>
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
        <footer>
            <?php include("templates/footer.php"); ?>
        </footer>
    </div>
</main>
</body>
</html>
