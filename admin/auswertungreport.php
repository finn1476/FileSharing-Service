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

// Get list of available report files
$reportFiles = glob('../sicherspeicher/report*.csv');

// Set default selected file to reports.csv
$selectedFile = '../sicherspeicher/reports.csv';
if (isset($_POST['file_select'])) {
    $selectedFile = $_POST['file_select'];
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
                    padding: 0.5rem; /* Adjust padding */
                    font-size: 0.8rem; /* Adjust font size */
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

                .greeen {
                    border: 1px solid #ddd;
                    background-color: green;
                }

                .orange {
                    background-color: orange;
                    padding: 1.5rem;
                }

                .black {
                    background-color: black;
                }

                .blue {
                    background-color: blue;
                    padding: 1.5rem;
                }
            </style>

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
                        $rowClass = ($report['reason'] == 'Child Porno Pornography') ? 'red' : '';
                        $fileDisabled = isFileDisabled($report['filename']);
                        $username = getUserForFile($report['filename'], $userCsvFile);
                        $isUserFile = !is_null($username);

                        echo "<tr class='$rowClass'>";
                        echo "<td>{$report['id']}</td>";
                        echo "<td>{$report['case_number']}</td>";
                        echo "<td>{$report['email']}</td>";
                        echo "<td>{$report['filename']}</td>";
                        echo "<td>{$report['description']}</td>";
                        echo "<td>{$report['reason']}</td>";
                        echo "<td>{$report['passwords']}</td>";
                        echo "<td>{$report['created_at']}</td>";
                        echo "<td class='" . ($fileDisabled ? 'greeen' : ($fileFound ? 'orange' : 'black')) . "'>";
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
