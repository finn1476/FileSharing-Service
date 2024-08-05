<?php
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

// Function to get the list of existing users
function getExistingUsers() {
    $htpasswdFile = '.htpasswd';

    // Read the current .htpasswd file
    $htpasswdData = file($htpasswdFile, FILE_IGNORE_NEW_LINES);

    $users = array();
    foreach ($htpasswdData as $line) {
        list($username, $hash) = explode(':', $line, 2);
        $users[] = $username;
    }

    return $users;
}

// Get the list of existing users
$existingUsers = getExistingUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Existing Users</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <main>
        <div class="awasr">
            <h1>Existing Users</h1>

            <?php
            if (!empty($existingUsers)) {
                echo "<ul>";
                foreach ($existingUsers as $user) {
                    echo "<li>$user</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No existing users found.</p>";
            }
            ?>

            <p><a class="buttona" href="adminpanel3.php">Back to User Management</a></p>
        </div>
    </main>
</body>
</html>
