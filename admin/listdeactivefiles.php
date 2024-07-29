<?php
// Include necessary functions and logic
function isHashInCSV($hashValue, $csvFile) {
    $csvData = file_get_contents($csvFile);
    $hashes = explode(PHP_EOL, $csvData);

    // Check if the hash is in the CSV file
    return in_array($hashValue, $hashes);
}

// Directory where files are stored
$filesDirectory = '../Files/';

// CSV file containing hashes
$csvFile = '../Speicher/hashes.csv';

// Get all files in the "Files" directory
$allFiles = scandir($filesDirectory);

// Get the hashes from the CSV file
$csvData = file_get_contents($csvFile);
$hashesInCSV = explode(PHP_EOL, $csvData);

// Find deactivated files that still exist in the "Files" directory
$deactivatedFiles = array_filter($allFiles, function ($filename) use ($hashesInCSV, $filesDirectory) {
    $fileFullPath = $filesDirectory . $filename;
    
    // Check if the file is not a directory and is deactivated
    return !is_dir($fileFullPath) && isHashInCSV(hash_file('sha256', $fileFullPath), '../Speicher/hashes.csv');
});

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Panel - Deleted Decision</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="../style.css" />
</head>

<body>
    <main>
        <div class="awasr">
            <h1>Admin Panel - Deleted Decision</h1>
            <h2>List of Deactivated Files Still in "Files" Directory:</h2>
            
            <?php
            if (empty($deactivatedFiles)) {
                echo "<p>No deactivated files found in the 'Files' directory.</p>";
            } else {
                echo "<ul>";
                foreach ($deactivatedFiles as $file) {
                    $filename = urlencode($file); // Encode filename for URL
                    echo "<li>$file <a href='admindeletedesision.php?filename=$filename'><button>Manage File</button></a></li>";
                }
                echo "</ul>";
            }
            ?>
        </div>
    </main>
    <footer class="footera">
        <!-- Your footer content here -->
    </footer>
</body>

</html>
