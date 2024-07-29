<?php
    $downloadFilename = $_GET['filename'] ?? null;
    $downloadPath = 'Files/' . $downloadFilename;
    $currentDomain = $_SERVER['HTTP_HOST'];
    $disabledFiles = file('disabled_files.txt', FILE_IGNORE_NEW_LINES);
    
if (file_exists($downloadPath)) {
        // Setzen Sie den richtigen Content-Type Header für die Datei
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($downloadPath) . "\""); 
    
        // Senden Sie die Datei an den Client
        readfile($downloadPath);
    } else {
        echo "Die angeforderte Datei existiert nicht.";
    }

?>