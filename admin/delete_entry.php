<?php
// Turn off error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Include the database configuration file
include 'config.php';

if (isset($_GET['id'])) {
    $idToDelete = intval($_GET['id']); // Ensure the ID is an integer

    // Prepare SQL DELETE statement
    $sql = 'DELETE FROM reports WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $idToDelete, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Optional: Print a success message for debugging
        // echo 'Entry deleted successfully.';
    } else {
        // Optional: Print an error message for debugging
        // echo 'Error deleting entry.';
    }
} else {
    // Optional: Print an error message for debugging
    // echo 'No ID provided.';
}

// Redirect back to the admin panel
header('Location: adminpanel5.php');
exit();
?>
