<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Geschützter Bereich</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #333;
            color: #fff;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #444;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #555;
            text-align: left;
        }

        th {
            background-color: #666;
            font-weight: bold;
        }

        td a {
            color: #9cf;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        .logout {
            text-align: center;
            margin-top: 20px;
        }

        .logout a {
            color: #f88;
            text-decoration: none;
        }

        .logout a:hover {
            text-decoration: underline;
        }

        .delete-button {
            background-color: #f00;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #c00;
        }
        .download{
            background-color: green;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<main>
    <div class="container">
        <?php
		error_reporting(0);
        session_start();

        if (!isset($_SESSION['username'])) {
            header("Location: index.html");
            exit;
        }
        ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
       
        <h2>Ihre hochgeladenen Dateien:</h2>
        <table>
            <tr>
                <th>Filename</th>
                <th>Download</th>
                <th>Delete</th>
            </tr>
            <?php
            $uploadDir = 'Files/';

            // Read all files from the CSV file
            $filesData = readFilesFromCsv('../Uploaded_Files/files.csv');
            $files = array();
            foreach ($filesData as $fileData) {
                // Check if the username matches the current user
                if ($fileData[1] == $_SESSION['username']) {
                    $files[] = $fileData[0]; // Add the file name to the list
                }
            }

            // Function to read files from CSV file
            function readFilesFromCsv($csvFile) {
                $filesData = array();
                if (($handle = fopen($csvFile, 'r')) !== false) {
                    while (($row = fgetcsv($handle)) !== false) {
                        $filesData[] = $row;
                    }
                    fclose($handle);
                } else {
                    echo "Error opening CSV file: $csvFile";
                }
                return $filesData;
            }

            // PHP-Code zum Löschen des Benutzers aus .htpasswd
            function deleteUserFromHtpasswd($username) {
                $htpasswdFile = '.htpasswd';
                $htpasswd = file($htpasswdFile);
                foreach ($htpasswd as $key => $line) {
                    $parts = explode(':', $line);
                    if ($parts[0] === $username) {
                        unset($htpasswd[$key]);
                        break;
                    }
                }
                file_put_contents($htpasswdFile, implode('', $htpasswd));
            }

            foreach ($files as $file) : ?>
                <tr>
                    <td><?php echo $file; ?></td>
                    <td><a class="download" href="../download.php?filename=<?php echo urlencode($file); ?>" target="_blank">Download</a></td>
                    <td>
                        <?php if ($file !== 'delete.php') : ?>
                            <button class="delete-button" data-filename="<?php echo $file; ?>" data-action="delete-file">Delete</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="logout">
            <a href="logout.php">Logout</a>
            <form id="deleteForm" action="delete_account.php" method="post">
                <!-- Andere Formularfelder hier -->
                <input type="hidden" name="delete_account" value="true">
                <input type="hidden" name="username" value="<?php echo $_SESSION['username']; ?>">
                <button class="delete-button delete-account-btn" type="button" data-action="delete-account">Delete account</button>
            </form>
        </div>
    </div>
</main>
<footer class="footer">
    <?php include("../templates/footeruser.php"); ?>
</footer>
<script>
    // JavaScript code to handle button clicks
    document.querySelectorAll('.delete-button, .delete-account-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var filename = this.getAttribute('data-filename');
            var action = this.getAttribute('data-action');
            if (confirm('Are you sure that you want to delete ' + (filename ? filename : 'Your account') + ' ? This action cannot be undone.')) {
                if (action === 'delete-file') {
                    window.location.href = 'delete.php?filename=' + encodeURIComponent(filename);
                } else if (action === 'delete-account') {
                    var form = document.getElementById('deleteForm');
                    form.action = 'delete_account.php'; // Setze die korrekte Aktion
                    form.submit();
                }
            }
        });
    });
</script>
</body>
</html>
