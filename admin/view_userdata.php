<?php
// Turn off error reporting

function getUserFiles($username, $csvFile) {
    if (!file_exists($csvFile)) {
        return [];
    }

    $csvData = file_get_contents($csvFile);
    $lines = explode(PHP_EOL, $csvData);
    $userFiles = [];

    foreach ($lines as $line) {
        $data = str_getcsv($line);
        if (isset($data[1]) && trim($data[1]) === $username) {
            $userFiles[] = [
                'filename' => $data[0]
            ];
        }
    }

    return $userFiles;
}

function getFileInfo($filename, $csvFile) {
    if (!file_exists($csvFile)) {
        return null;
    }

    $csvData = file_get_contents($csvFile);
    $lines = explode(PHP_EOL, $csvData);

    foreach ($lines as $line) {
        $data = str_getcsv($line);
        if (isset($data[3]) && trim($data[3]) === $filename) {
            return [
                'id' => trim($data[0]),
                'caseNumber' => trim($data[1]),
                'email' => trim($data[2])
            ];
        }
    }

    return null;
}

function calculateAndStoreFileHash($filename) {
    $filePath = '../Files/' . $filename;
    if (file_exists($filePath)) {
        $hash = hash_file('sha256', $filePath);
        $hashCsvFile = '../Speicher/hashes.csv';

        $fileHandle = fopen($hashCsvFile, 'a');
        if ($fileHandle) {
            fputcsv($fileHandle, [$filename, $hash]);
            fclose($fileHandle);
        }
    }
}

// Get filename from POST request
$filename = isset($_POST['filename']) ? $_POST['filename'] : null;

// Paths to the CSV files
$userCsvFile = '../Uploaded_Files/files.csv';
$reportsCsvFile = '../sicherspeicher/reports.csv';

$userFiles = [];
$username = '';
$email = '';
$caseNumber = '';
$id = '';
$allFilenames = []; // Array to store all filenames for sending in a single email
$allIds[] = $id;
$allCaseNumbers[] = $caseNumber;

if ($filename) {
    // Retrieve the username for the given filename from the user files CSV
    $csvData = file_get_contents($userCsvFile);
    $lines = explode(PHP_EOL, $csvData);
    foreach ($lines as $line) {
        $data = str_getcsv($line);
        if (isset($data[0]) && trim($data[0]) === $filename) {
            $username = trim($data[1]);
            break;
        }
    }

    // Get all files for the user
    if ($username) {
        $userFiles = getUserFiles($username, $userCsvFile);
    }

    // Retrieve the email, caseNumber, and id for the given filename from the reports CSV
    $fileInfo = getFileInfo($filename, $reportsCsvFile);
    if ($fileInfo) {
        $email = $fileInfo['email'];
        $caseNumber = $fileInfo['caseNumber'];
        $id = $fileInfo['id'];
    }

    // Collect all filenames for sending in a single email
    foreach ($userFiles as $file) {
        $allFilenames[] = $file['filename'];
    }
    // Collect all IDs and case numbers for inclusion in email subject
    foreach ($userFiles as $file) {
        $fileInfo = getFileInfo($file['filename'], $reportsCsvFile);
        if ($fileInfo) {
            $allIds[] = $fileInfo['id'];
            $allCaseNumbers[] = $fileInfo['caseNumber'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Benutzerdaten</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <main>
        <div class="awasr">
            <div><h2>Benutzerdaten für Benutzer: <?php echo htmlspecialchars($username); ?></h2><br></div>
            <div class="maske"><img src="../bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
            <h1>Benutzerdaten</h1>

            <?php if (!empty($userFiles)): ?>
                <table>
                    <tr>
                        <th>Dateiname</th>
                        <th>Aktionen</th>
                    </tr>
                    <?php foreach ($userFiles as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['filename']); ?></td>
                            <td>
                                <button class="green" onclick="manageFile('<?php echo htmlspecialchars($file['filename']); ?>')">Manage File</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <button class="blue" onclick="sendMailAll('<?php echo htmlspecialchars($email); ?>', '<?php echo htmlspecialchars(implode(",", $allCaseNumbers)); ?>', '<?php echo htmlspecialchars(implode(",", $allIds)); ?>')">Send Email for All Files</button>
                <button class="red" onclick="deleteUser('<?php echo htmlspecialchars($username); ?>')">Delete User and Files</button>
            <?php else: ?>
                <p>Keine Dateien für diesen Benutzer gefunden.</p>
            <?php endif; ?>

            <p><a class="buttona" href="adminpanel3.php">Zurück</a>
            <a class="buttona" href="../index.php">HOME</a></p>
            <a class="buttona" href="auswertung.php">Auswertung</a>
        </div>
    </main>

    <script>
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

        function deleteUser(username) {
            if (confirm("Sind Sie sicher, dass Sie diesen Benutzer und alle seine Dateien löschen möchten?")) {
                <?php foreach ($userFiles as $file): ?>
                    <?php calculateAndStoreFileHash($file['filename']); ?>
                <?php endforeach; ?>

                var form = document.createElement("form");
                form.method = "POST";
                form.action = "delete_user.php";

                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "username";
                input.value = username;

                form.appendChild(input);
                document.body.appendChild(form);

                form.submit();
            }
        }

        function sendMailAll(email, caseNumbers, ids) {
            // Split the comma-separated values into arrays
            var caseNumbersArray = caseNumbers.split(",");
            var idsArray = ids.split(",");

            // Construct the subject
            var subject = "Delete Request " + caseNumbersArray.join(", #") + ", ID " + idsArray.join(", #");

            // If there are no case numbers, remove the leading comma and space
            if (subject.indexOf("#,") === 13) {
                subject = subject.slice(0, 12) + subject.slice(13);
            }

            // Construct the body
            var body = "Dear Sir/Madam,\n\nPlease be advised that your delete request has been deemed valid for the following files:\n\n";
            body += "<?php echo implode(", ", $allFilenames); ?>\n\nBest regards,\nAdmin Team";

            // Open the mail client with the subject and body
            window.location.href = "mailto:" + email + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);
        }
    </script>
</body>
</html>
