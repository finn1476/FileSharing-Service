<!DOCTYPE html>
<html>
<head>
    <title>Title</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="styles.css" />
</head>

<body>
<?php
header("Refresh:2;index.php");


// Überprüfen, ob die Checkbox mit dem Namen "check" gesetzt wurde

    $awdsa = $_POST["check"];
    echo $awdsa;
	if ($awdsa == "1") 
	{
	$datei = fopen("../Uploaded_Files/statusupload.csv","w");
	fputs($datei,$awdsa);
	fclose($datei);
	}else 
{
	$datei = fopen("../Uploaded_Files/statusupload.csv","w");
	echo "0";
	fputs($datei,"0");
	fclose($datei);
}
	
	
	
	
?>
</body>
</html>
