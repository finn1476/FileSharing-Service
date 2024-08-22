<?php
session_start();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Protected Area</title>

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

      h1 {
        color: var(--primary-color);
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      .container {
        background-color: white;
        border: 1px solid var(--border-color);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
      }

      p {
        margin-bottom: 20px;
      }

      table {
        width: 100%;
        border-collapse: collapse;
      }

      th, td {
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
        text-align: left;
      }

      th {
        background-color: var(--primary-color);
        color: #fff;
        font-weight: bold;
      }

      td a {
        color: var(--button-color);
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
        color: var(--accent-color);
        text-decoration: none;
        margin-right: 15px;
      }

      .logout a:hover {
        text-decoration: underline;
      }

      .delete-button, .delete-account-btn {
        background-color: var(--error-color);
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
      }

      .delete-button:hover, .delete-account-btn:hover {
        background-color: darkred;
      }

      .download {
        background-color: var(--button-color);
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
      }

      .download:hover {
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
        <a href="../index.php">Home</a>
        <a href="../pricing.php">Pricing</a>
        <a href="statistic.php">Statistics</a>
    </nav>
</header>
<main>
    <div class="container">
        <?php
        error_reporting(0);

        if (!isset($_SESSION['username'])) {
            header("Location: index.html");
            exit;
        }
        ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <h2>Your Uploaded Files:</h2>
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

            foreach ($files as $file) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($file); ?></td>
                    <td><a class="download" href="../download.php?filename=<?php echo urlencode($file); ?>&key=" target="_blank">Download</a></td>
                    <td>
                        <?php if ($file !== 'delete.php') : ?>
                            <button class="delete-button" data-filename="<?php echo htmlspecialchars($file); ?>" data-action="delete-file">Delete</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="logout">
            <a href="logout.php">Logout</a>
            <form id="deleteForm" action="delete_account.php" method="post">
                <!-- Other form fields here -->
                <input type="hidden" name="delete_account" value="true">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
                <button class="delete-button delete-account-btn" type="button" data-action="delete-account">Delete account</button>
            </form>
        </div>
    </div>
</main>
<footer class="footer">
    <div class="footer-links">
      <a href="../FAQ.php">FAQ</a>
      <a href="../impressum.php">Imprint</a>
      <a href="../abuse.php">Abuse</a>
      <a href="../terms.php">ToS</a>
      <a href="../datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
</footer>

<script>
    // JavaScript code to handle button clicks
    document.querySelectorAll('.delete-button, .delete-account-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var filename = this.getAttribute('data-filename');
            var action = this.getAttribute('data-action');
            if (confirm('Are you sure you want to delete ' + (filename ? filename : 'your account') + '? This action cannot be undone.')) {
                if (action === 'delete-file') {
                    window.location.href = 'delete.php?filename=' + encodeURIComponent(filename);
                } else if (action === 'delete-account') {
                    var form = document.getElementById('deleteForm');
                    form.action = 'delete_account.php'; // Set correct action
                    form.submit();
                }
            }
        });
    });
</script>
</body>
</html>
