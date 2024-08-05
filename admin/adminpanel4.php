<?php
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

// Function to read selected file types from CSV file
function readSelectedFileTypesFromCsv($csvFile) {
    $selectedFileTypesData = array();
    if (($handle = fopen($csvFile, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $selectedFileTypesData[] = $row;
        }
        fclose($handle);
    } else {
        echo "Error opening CSV file: $csvFile";
    }
    return $selectedFileTypesData;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data and save to CSV
    if (isset($_POST['filetypes']) && is_array($_POST['filetypes'])) {
        $selectedFileTypes = $_POST['filetypes'];

        // Open or create CSV file
        $csvFile = fopen("../Speicher/selected_filetypes.csv", "w");

        // Write data to CSV
        fputcsv($csvFile, $selectedFileTypes);

        // Close CSV file
        fclose($csvFile);
    }
}

// Read selected file types from CSV file
$selectedFileTypesData = readSelectedFileTypesFromCsv("../Speicher/selected_filetypes.csv");
$selectedFileTypes = isset($selectedFileTypesData[0]) ? $selectedFileTypesData[0] : array();
$additionalFileTypes = array(
    "doc", "docx", "xls", "xlsx", "ppt", "pptx", "mp3", "wav", "jpg", "jpe", "png", "gif","webp", "pdf", "txt", "zip","rar", 
    "mp4", "avi", "mov", "flv", "ogg", "html", "css", "js", "php", "cpp", "java", "py", "rb", "json", 
    "xml", "csv", "svg", "psd", "ai", "eps", "docm", "dot", "dotm", "xlsb", "xlsm", "xlt", "xltm", "potx", "potm", 
    "pptm", "odt", "ods", "odp", "odg", "odf", "odc", "odb", "odx", "rtf", "sxc", "sxi", "sxd", "sxi", "sxm", "log"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <style>
        table {
            border-collapse: collapse;
        }

        
    </style>
</head>
<body>
    <main>
        <div class="awasr">
            <div><h2>Anonymer File Upload</h2><br></div>
            <div class="maske"><img src="../bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
            <h1>Admin Panel</h1>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Select File Types:</label><br>
                <table>
                    <tr>
                        <?php
                            $checkboxCount = 0;
                            foreach ($additionalFileTypes as $fileType):
                                if ($checkboxCount % 6 == 0 && $checkboxCount > 0) {
                                    echo "</tr><tr>";
                                }
                        ?>
                                <td><label><input type="checkbox" name="filetypes[]" value="<?php echo $fileType; ?>" <?php if (in_array($fileType, $selectedFileTypes)) echo 'checked'; ?>> <?php echo strtoupper($fileType); ?></label></td>
                        <?php
                                $checkboxCount++;
                            endforeach;
                        ?>
                    </tr>
                </table>
				<br><br>
                <center><input type="submit" value="Submit"></center><br>
            </form>

            <p><a class="buttona" href="adminpanel3.php">Zurück</a>
            <a class="buttona" href="adminpanel5.php">Nächste Seite</a></p>
            <a class="buttona" href="index.php">HOME</a>
        </div>
    </main>

  <footer class="footera">
<div>
<h1 class="right"><a class="bauttona" href="adminpanel5.php">Statistiken</a></h1>
</div>
<div>
<h1 class="right"><a class="bauttona" href="adminpanel4.php">Datei-Typen</a></h1>
</div>
<div>
<h1 class="right"><a class="bauttona" href="adminpanel3.php">Benutzer-Verwaltung</a></h1>
</div>
<div>
<h1 class="right"><a class="bauttona" href="adminpanel2.php">Upload-Grenze</a></h1>
</div>
<div>
<h1><a class="bauttona" href="admindelete.php">Löschen</a></h1>
</div>

</footer>
</body>
</html>
