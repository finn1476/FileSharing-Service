<?php
// Turn off error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Include database configuration
include 'config.php';

function getReports($pdo) {
    $reports = [];

    // SQL query to fetch all reports including new fields
    $sql = 'SELECT id, case_number, email, filename, description, reason, passwords, created_at FROM reports ORDER BY id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $reports[] = $row;
    }

    return $reports;
}

function isFileDisabled($filename) {
    // Implement logic to check if the file is disabled
    $filePath = '../Files/' . $filename;
    if (!file_exists($filePath)) {
        return true; // File not found
    }
    
    $hashValue = hash_file('sha256', $filePath);
    $csvFile = '../speicher/hashes.csv';

    return isHashInCSV($hashValue, $csvFile);
}

function isHashInCSV($hashValue, $csvFile) {
    $csvData = file_get_contents($csvFile);
    $hashes = explode(PHP_EOL, $csvData);

    // Check if the hash is in the CSV file
    return in_array($hashValue, $hashes);
}

function getUserForFile($filename, $csvFile) {
    // Check if the file belongs to a user by reading the CSV
    if (!file_exists($csvFile)) {
        return null;
    }

    $csvData = file_get_contents($csvFile);
    $lines = explode(PHP_EOL, $csvData);

    foreach ($lines as $line) {
        $data = explode(',', $line);
        if (isset($data[0]) && trim($data[0]) === $filename) {
            return isset($data[1]) ? trim($data[1]) : null;
        }
    }

    return null;
}

function getReportAgeClass($createdAt) {
    $createdTime = strtotime($createdAt);
    $currentTime = time();
    $ageInHours = ($currentTime - $createdTime) / 3600;

    if ($ageInHours > 48) {
        return 'orange'; // Report is older than 48 hours
    }

    return ''; // Report is not older than 48 hours
}

// Get list of available report files
$reportFiles = glob('../sicherspeicher/report*.csv');

// Set default selected file to reports.csv
$selectedFile = '../sicherspeicher/reports.csv';
if (isset($_POST['file_select'])) {
    $selectedFile = $_POST['file_select'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

      main {
        max-width: 100000px;
        margin: 50px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      h1, h2 {
        color: var(--primary-color);
        margin-bottom: 20px;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        background-color: var(--background-color);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
      }

      th, td {
        border: 1px solid var(--border-color);
        padding: 10px;
        text-align: left;
      }

      th {
        background-color: var(--primary-color);
        color: white;
      }

      .red {
        color: white;
        background-color: var(--error-color);
        border-color: var(--error-color);
		outline:white solid 1px;
      }

      .green {
        color: white;
        background-color: var(--button-color);
        border-color: var(--button-color);
		outline:white solid 1px;
      }

      .greeen {
        border: 1px solid var(--border-color);
        background-color: var(--button-color);
      }

      .orange {
        background-color: var(--accent-color);
        color: white;
		outline:white solid 1px;
      }

      .black {
        background-color: black;
        color: white;
      }

      .blue {
        background-color: var(--button-color);
        color: white;
		outline:white solid 1px;
      }

      .file-not-found {
        background-color: var(--button-color);
        color: white;
      }

      button {
        border: none;
        padding: 10px 15px;
        color: white;
        font-size: 14px;
        cursor: pointer;
        border-radius: 5px;
        margin: 5px;
        transition: background-color 0.3s ease;
      }

      button:hover {
        background-color: var(--button-hover-color);
      }

      a.buttona {
        display: inline-block;
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin: 5px;
        transition: background-color 0.3s ease;
      }

      a.buttona:hover {
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

      @media (max-width: 600px) {
        footer .footer-links {
          flex-direction: column;
          gap: 10px;
        }
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
<body>
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

    <main>
        <div class="awasr">
            <h1>Admin Panel</h1>
            <h2>Anonymer File Upload</h2>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Case Number</th>
                    <th>Email</th>
                    <th>File Name</th>
                    <th>Description</th>
                    <th>Reason</th>
                    <th>Passwords</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
                <?php
                // Read data from the database and display entries in a table
                $reports = getReports($pdo);
                $userCsvFile = '../Uploaded_Files/files.csv'; // This CSV contains the filename and username

                foreach ($reports as $report) {
                    if (!empty($report['id']) && !empty($report['case_number']) && !empty($report['email']) && !empty($report['filename']) && !empty($report['description']) && !empty($report['reason'])) {
                        $rowClass = ($report['reason'] == 'Child Pornography') ? 'red' : '';
                        $fileDisabled = isFileDisabled($report['filename']);
                        $username = getUserForFile($report['filename'], $userCsvFile);
                        $isUserFile = !is_null($username);
                        $reportAgeClass = getReportAgeClass($report['created_at']);
                        $rowClass .= $fileDisabled ? ' file-not-found' : '';
                        $rowClass .= $reportAgeClass ? ' ' . $reportAgeClass : '';

                        echo "<tr class='$rowClass'>";
                        echo "<td>{$report['id']}</td>";
                        echo "<td>{$report['case_number']}</td>";
                        echo "<td>{$report['email']}</td>";
                        echo "<td>{$report['filename']}</td>";
                        echo "<td>{$report['description']}</td>";
                        echo "<td>{$report['reason']}</td>";
                        echo "<td>{$report['passwords']}</td>";
                        echo "<td>{$report['created_at']}</td>";
                        echo "<td>";
                        if ($isUserFile) {
                            echo "<input type='checkbox' onclick=\"viewUserData('{$report['filename']}')\"> Nutzerinformationen ansehen";
                        }
                        echo "<button class='red' onclick=\"deleteEntry('{$report['id']}')\">Delete</button>";
                        echo "<button class='green' onclick=\"manageFile('{$report['filename']}', '{$report['passwords']}')\">Manage File</button>";
                        echo "<button class='orange' onclick=\"sendMailA('{$report['email']}', '{$report['filename']}', '{$report['case_number']}', '{$report['id']}')\">Mail Accept</button>";
                        echo "<button class='blue' onclick=\"sendMailB('{$report['email']}', '{$report['filename']}', '{$report['case_number']}', '{$report['id']}')\">Mail Deny</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>

            <p>
                <a class="buttona" href="adminpanel3.php">Zurück</a>
                <a class="buttona" href="../index.php">HOME</a>
            </p>
        </div>
    </main>

    <footer>
        <div class="footer-links">
            <a href="index.php">Linkpage</a>
            <a href="../index.php">Home</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
    
    <script>
    function deleteEntry(id) {
        var confirmDelete = confirm("Are you sure you want to delete this entry?");
        if (confirmDelete) {
            // Send a request to delete the entry
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Reload the page after successful deletion
                    location.reload();
                }
            };
            xhttp.open("GET", "delete_entry.php?id=" + id, true);
            xhttp.send();
        }
    }

    function manageFile(filename, password) {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = "admindeletedesision.php";

        var filenameInput = document.createElement("input");
        filenameInput.type = "hidden";
        filenameInput.name = "filename";
        filenameInput.value = filename;
        form.appendChild(filenameInput);

        var passwordInput = document.createElement("input");
        passwordInput.type = "hidden";
        passwordInput.name = "password";
        passwordInput.value = password;
        form.appendChild(passwordInput);

        document.body.appendChild(form);
        form.submit();
    }

    function viewUserData(filename) {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = "view_userdata.php";

        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "filename";
        input.value = filename;

        form.appendChild(input);
        document.body.appendChild(form);

        form.submit();
    }

    function sendMailA(email, filename, caseNumber, id) {
        var subject = "Delete Request #" + caseNumber + " ID #" + id;
        var body = "Dear Sir/Madam,\n\n Please be advised that your delete request has been deemed valid for the file: " + filename + "\n\nBest regards,\nAdmin Team";
        window.location.href = "mailto:" + email + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);
    }

    function sendMailB(email, filename, caseNumber, id) {
        var subject = "Delete Request #" + caseNumber + " ID #" + id;
        var body = "Dear Sir/Madam,\n\n Please be advised that your delete request has been deemed invalid for the file: " + filename + "\n\nBest regards,\nAdmin Team";
        window.location.href = "mailto:" + email + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);
    }
    </script>
</body>
</html>
