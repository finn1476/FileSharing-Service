<?php
// Include the necessary functions and logic
function deleteFile($filename) {
    // Implement logic to delete the file
    // You may use the $filename to locate and delete the file
    // Add your logic here
    $fileToDelete = '../Files/' . $filename;

    if (file_exists($fileToDelete)) {
        // Delete the file
        addToCSV($filename);
		
		unlink($fileToDelete);

        // Add the hash to the CSV file
    } else {
        echo "<p>Error: The file '$filename' does not exist.</p>";
    }
}

function addToCSV($filename) {
    // Implement logic to add the file hash to the CSV file
    // You may use the $filename to get the hash and other details
    // Add your logic here

    $csvFile = '../Speicher/hashes.csv';
    $hashValue = hash_file('sha256', '../Files/' . $filename);

    // Check if the hash is already in the CSV file
    if (!isHashInCSV($hashValue, $csvFile)) {
        // Save the hash value in the CSV file
        $csvData = file_get_contents($csvFile);
        $csvData .= $hashValue . PHP_EOL; // Add only the hash value, not the filename
        file_put_contents($csvFile, $csvData, LOCK_EX);
    }
}

// Function to check if the hash is in the CSV file
function isHashInCSV($hashValue, $csvFile) {
    $csvData = file_get_contents($csvFile);
    $hashes = explode(PHP_EOL, $csvData);

    // Check if the hash is in the CSV file
    return in_array($hashValue, $hashes);
}

// Get the filename from the URL parameter
$downloadFilename = $_POST["filename"];

// Handle the form submission
if (isset($_POST['confirmDelete'])) {
    $filename = $_POST['filename'];
    deleteFile($filename);

    // Redirect back to admindelete.php
    header("Location: admindelete.php");
    exit();  // Ensure that the script stops here to prevent further output
}

// Output the HTML structure
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Delete Confirmation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="../style.css" />
</head>

<body>
    <main>
        <div class="awasr">
            <h1>Delete Confirmation</h1>
			<?php
            $downloadFilename = $_POST["filename"];

            // Check if it's an image file
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];
            $audioExtensions = ['mp3', 'ogg', 'wav']; // Add audio extensions
            $fileExtension = pathinfo($downloadFilename, PATHINFO_EXTENSION);

            if (in_array(strtolower($fileExtension), $imageExtensions)) {
                echo "<p>Preview of the file: <br/><img class='picture-preview' src='../download_handler.php?filename=$downloadFilename' alt='File Preview' ></p>";
            } elseif (in_array(strtolower($fileExtension), $videoExtensions)) {
                echo "<p>Preview of the file:</p>";
                echo "<div class='preview-container'>";
                echo "<video class='picture-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='../download_handler.php?filename=$downloadFilename' type='video/mp4'></video>";
                echo "</div>";
            } elseif (in_array(strtolower($fileExtension), $audioExtensions)) {
                echo "<p>Preview of the file:</p>";
                echo "<div class='preview-container'>";
                echo "<audio class='audio-preview' controls ontimeupdate='limitPlayTime(this, 10)'><source src='../download_handler.php?filename=$downloadFilename' type='audio/mpeg'></audio>";
                echo "</div>";
            }
            ?>
            <p>Are you sure you want to delete the file: <?php echo $downloadFilename; ?>?</p>
            <form action="admindelete_confirm.php" method="post">
                <input type="hidden" name="filename" value="<?php echo $downloadFilename; ?>">
				<input type="hidden" name="confirmDelete" value="confirmDelete">
                <button type="submit" name="confirmDelete">Yes, Delete</button>
            </form>
        </div>
    </main>
</body>

</html>
