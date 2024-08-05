<?php
// Turn off error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

function getReports($csvFile) {
    // If the file does not exist, return an empty array
    if (!file_exists($csvFile)) {
        return [];
    }

    // Read CSV file and parse entries
    $csvData = file_get_contents($csvFile);
    $lines = explode(PHP_EOL, $csvData);
    $reports = [];

    foreach ($lines as $line) {
        $data = explode(',', $line);

        // Create an associative array with meaningful keys
        $report = [
            'id' => isset($data[0]) ? intval($data[0]) : null,
            'case_number' => isset($data[1]) ? $data[1] : null,
            'email' => isset($data[2]) ? $data[2] : null,
            'filename' => isset($data[3]) ? $data[3] : null,
            'description' => isset($data[4]) ? $data[4] : null,
            'reason' => isset($data[5]) ? $data[5] : null,
        ];

        $reports[] = $report;
    }

    return $reports;
}

function renameReportsFile() {
    $sourceFile = '../sicherspeicher/reports.csv';
    $targetFile = getNextReportFileName();
    if (rename($sourceFile, $targetFile)) {
        echo "Datei erfolgreich umbenannt von $sourceFile zu $targetFile.";
    } else {
        echo "Fehler beim Umbenennen der Datei.";
    }
}

if (isset($_POST['rename_reports'])) {
    // Check if the user confirms the action
    echo "<script>";
    echo "if (confirm('Sind Sie sicher, dass Sie die Datei umbenennen möchten?')) {";
    echo "window.location.href = 'auswertung.php?confirm_rename=true';";
    echo "} else {";
    echo "window.location.href = 'auswertung.php';";
    echo "}";
    echo "</script>";
}

if (isset($_GET['confirm_rename']) && $_GET['confirm_rename'] == 'true') {
    renameReportsFile();
}

function isFileDisabled($filename) {
    // Implement logic to check if the file is disabled
    // You may use the $filename to check if the file is disabled
    // Add your logic here
    $hashValue = hash_file('sha256', '../Files/' . $filename);
    $csvFile = '../speicher/hashes.csv';

    return isHashInCSV($hashValue, $csvFile);
}

function isHashInCSV($hashValue, $csvFile) {
    $csvData = file_get_contents($csvFile);
    $hashes = explode(PHP_EOL, $csvData);

    // Check if the hash is in the CSV file
    return in_array($hashValue, $hashes);
}

// Function to find the next available report file name
function getNextReportFileName() {
    $index = 1;
    $prefix = '../sicherspeicher/report';
    $extension = '.csv';
    $fileName = $prefix . str_pad($index, 3, '0', STR_PAD_LEFT) . $extension;

    // Keep incrementing the index until a non-existing file is found
    while (file_exists($fileName)) {
        $index++;
        $fileName = $prefix . str_pad($index, 3, '0', STR_PAD_LEFT) . $extension;
    }

    return $fileName;
}

// Function to get all CSV files in the directory
function getCSVFiles() {
    $directory = '../sicherspeicher/';
    $files = scandir($directory);
    $csvFiles = [];

    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'csv') {
            $csvFiles[] = $directory . $file;
        }
    }

    return $csvFiles;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <main>
        <div class="awasr">
            <div><h2>Anonymer File Upload</h2><br></div>
            <div class="maske"><img src="../bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
            <h1>Admin Panel</h1>

            <style>
                table {
                    border-collapse: collapse;
                    margin: 20px;
                }

                th, td {
                    border: 1px solid #ddd;
                    padding: 1rem;
                    text-align: left;
                }

                th {
                    background-color: #4caf50; /* Green */
                    color: white;
                }

                .red {
                    color: white;
                    background-color: red;
                    border-color: red;
                    padding: 1.5rem;
                }

                .green {
                    color: white;
                    background-color: green;
                    border-color: green;
                    padding: 1.5rem;
                }
                .greeen{
                    border: 1px solid #ddd;
                    background-color: green;
                }

                .orange {
                    background-color: orange;
                }

                .black {
                    background-color: black;
                }
            </style>

            <!-- Dropdown-Menü für die Auswahl der Datei -->
            <form method="post" action="auswertung.php">
                <label for="file_select">Datei auswählen:</label>
                <select name="file_select" id="file_select">
                    <?php
                    $csvFiles = getCSVFiles();
                    foreach ($csvFiles as $file) {
                        $fileName = basename($file);
                        echo "<option value='$file'>$fileName</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="select_file">Datei auswählen</button>
            </form>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Case Number</th>
                    <th>Email</th>
                    <th>File Name</th>
                    <th>Description</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
                <?php
                // Read CSV file and display entries in a table based on the selected file
                if (isset($_POST['select_file'])) {
                    $selectedFile = $_POST['file_select'];
                    $reports = getReports($selectedFile);

                    $caseNumberCount = 0;

                    foreach ($reports as $report) {
                        // Check if all fields except ID and Case Number are empty
                        if (empty($report['email']) && empty($report['filename']) && empty($report['description']) && empty($report['reason'])) {
                            $caseNumberCount++; // Increment the count of displayed case numbers
                            $rowClass = ($report['reason'] == 'Child Porno Pornography') ? 'red' : '';
                            $fileDisabled = isFileDisabled($report['filename']);

                            echo "<tr class='$rowClass'>";
                            echo "<td>{$report['id']}</td>";
                            echo "<td>{$report['case_number']}</td>";
                            echo "<td>{$report['email']}</td>";
                            echo "<td>{$report['filename']}</td>";
                            echo "<td>{$report['description']}</td>";
                            echo "<td>{$report['reason']}</td>";
                            echo "<td class='" . ($fileDisabled ? 'greeen' : ($fileFound ? 'orange' : 'black')) . "'>";
                            echo "<button class='red' onclick=\"deleteEntry('{$report['id']}')\">Delete</button>";
                            echo "<button class='green' onclick=\"manageFile('{$report['filename']}')\">Manage File</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                    $bearbeitetedatein = $caseNumberCount - 1;
                } else {
                    // Falls keine Datei ausgewählt wurde, wird standardmäßig reports.csv verwendet
                    $selectedFile = '../sicherspeicher/reports.csv';
                    $reports = getReports($selectedFile);
                    $bearbeitetedatein = count($reports) - 1; // Gesamtanzahl der bearbeiteten Meldungen
                }
                ?>
            </table>

            <!-- Display the count of displayed case numbers -->
            <p>Bearbeitete Meldungen: <?php echo $bearbeitetedatein; ?></p>

            <form method="post" action="auswertung.php">
                <button type="submit" name="rename_reports">Reports umbenennen</button>
            </form>

            <!-- Formular zum Herunterladen aller "reports.csv"-Dateien -->
            <form method="post" action="download_reports.php">
                <button type="submit" name="download_all_reports">Alle Reports herunterladen</button>
            </form>

            <p><a class="buttona" href="adminpanel3.php">Zurück</a>
            <a class="buttona" href="../index.php">HOME</a></p>

        </div>
    </main>

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

        function manageFile(filename) {
            var form = document.createElement("form");
            form.method = "POST";
            form.action = "admindeletedesision.php";

            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "filename";
            input.value = filename;

            form.appendChild(input);
            document.body.appendChild(form);

            form.submit();
        }
    </script>

</body>
</html>
