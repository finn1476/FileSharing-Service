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
    "doc", "docx", "xls", "xlsx", "ppt", "pptx", "mp3", "wav", "jpg", "jpe", "png", "gif", "webp", "pdf", "txt", "zip", "rar", 
    "mp4", "avi", "mov", "flv", "ogg", "html", "css", "js", "php", "cpp", "java", "py", "rb", "json", 
    "xml", "csv", "svg", "psd", "ai", "eps", "docm", "dot", "dotm", "xlsb", "xlsm", "xlt", "xltm", "potx", "potm", 
    "pptm", "odt", "ods", "odp", "odg", "odf", "odc", "odb", "odx", "rtf", "sxc", "sxi", "sxd", "sxi", "sxm", "log"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        background-color: var(--background-color);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .awasr label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
      }

      .awasr input[type="checkbox"] {
        margin-right: 8px;
      }

      .awasr table {
        width: 100%;
        border-collapse: collapse;
      }

      .awasr td {
        padding: 10px;
        border: 1px solid var(--border-color);
      }

      .awasr input[type="submit"] {
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      .awasr input[type="submit"]:hover {
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
        nav {
          flex-direction: column;
          gap: 10px;
        }

        .footer-links {
          flex-direction: column;
          gap: 10px;
        }
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
            <h2>Anonymer File Upload</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Select File Types:</label>
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
                <input type="submit" value="Submit">
            </form>
        </div>
    </main>
    <footer class="footer">
        <div class="footer-links">
            <a href="index.php">Linkpage</a>
            <a href="../index.php">Home</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
</body>
</html>
