<?php
if (isset($_GET['id'])) {
    $idToDelete = $_GET['id'];

    $csvFile = '../sicherspeicher/reports.csv';

    // Read existing data from the CSV file
    $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Remove the entry with the specified ID
    $updatedData = '';
    foreach ($lines as $line) {
        $data = explode(',', $line);
        $currentId = $data[0];

        if ($currentId == $idToDelete) {
            // Keep the ID and Case Number fields, only remove other fields
            $updatedData .= $data[0] . ',' . $data[1] . PHP_EOL;
        } else {
            // Preserve the existing line for entries with different IDs
            $updatedData .= $line . PHP_EOL;
        }
    }

    // Save the updated data back to the CSV file
    file_put_contents($csvFile, $updatedData, LOCK_EX);
}

// Redirect back to the admin panel
header('Location: adminpanel5.php');
exit();
?>
