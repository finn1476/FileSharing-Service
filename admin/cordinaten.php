<?php
// CSV Datei mit den Daten zur maximalen Dateigröße einlesen
$fileSizeFilePath = '../Speicher/filesgrosse.csv';
if (!file_exists($fileSizeFilePath)) {
    echo "Die Datei für die maximale Dateigröße existiert nicht.";
    exit;
}

$fileSizeData = file($fileSizeFilePath);
$maxFileSizeBytes = intval(trim($fileSizeData[0])); // Maximale erlaubte Dateigröße in Bytes
// Umrechnen der maximalen Dateigröße in Gigabytes
$maxFileSizeGB = round($maxFileSizeBytes / (1024 * 1024 * 1024), 2);

// CSV Datei mit den Daten zur Anzahl der Dateien einlesen
$csvFilePath = '../Speicher/file_stats.csv';
if (!file_exists($csvFilePath)) {
    echo "Die CSV-Datei existiert nicht.";
    exit;
}

$csvData = array_map('str_getcsv', file($csvFilePath));
// Die ersten Zeile (Kopfzeile) entfernen, da es die Spaltennamen sind
array_shift($csvData);

$dataPointsFileStats = array();
foreach ($csvData as $row) {
    $date = strtotime($row[0]);
    $fileCount = intval($row[1]); // Wert an Stelle 1 des Arrays
    $totalSizeInBytes = intval($row[2]);
    // Bytes in Gigabytes umrechnen
    $totalSizeInGB = round($totalSizeInBytes / (1024 * 1024 * 1024), 2);
    $dataPointsFileStats[] = array('x' => date('Y-m-d', $date), 'y' => $totalSizeInGB, 'label' => date('Y-m-d', $date), 'fileCount' => $fileCount);
}

// Daten für das Koordinatensystem aus filesgrosse.csv vorbereiten
$dataPointsFilesgrosse = array();
$date = date('Y-m-d'); // Annahme: Aktuelles Datum verwenden, wenn kein Datum in der Datei angegeben ist
$dataPointsFilesgrosse[] = array('x' => $date, 'y' => $maxFileSizeGB, 'label' => $date, 'maxFileSizeGB' => $maxFileSizeGB); // Hinzufügen von 'maxFileSizeGB' für die maximale Größe

// HTML und JavaScript für das Koordinatensystem ausgeben
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Datenvisualisierung</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<link rel="stylesheet" type="text/css" href="style.css" />
    <style>
        body {
            background-color: black;
            margin: 0;
            padding: 0;
        }
        html {
            background-color: black;
        }
        .container {
            display: flex;
            justify-content: space-around;
            padding: 20px;
        }
        .chart-container {
            width: 35%;
            height: 60vh;
            background-color: black;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <center><h1>Anonfiles - Statistiken</h1></center>
    <div class="container">
        <div class="chart-container">
            <canvas id="fileStatsChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="fileCountChart"></canvas>
        </div>
    </div>

    <script>
        // Daten für das Koordinatensystem aus file_stats.csv
        var fileStatsData = <?php echo json_encode($dataPointsFileStats); ?>;
        
        // Daten für das Koordinatensystem aus filesgrosse.csv
        var fileSizeData = <?php echo json_encode($dataPointsFilesgrosse); ?>;
        
        // Erweitern Sie die Daten für die maximale Größe zu einer Linie über das gesamte Koordinatensystem
        var maxFileSizeLineData = [];
        for (var i = 0; i < fileStatsData.length; i++) {
            maxFileSizeLineData.push({
                x: fileStatsData[i].x,
                y: fileSizeData[0].maxFileSizeGB, // Verwenden Sie den maximalen Wert für die Y-Koordinate
            });
        }

        // Koordinatensystem für die Gesamtgröße und maximale Größe
        var ctx1 = document.getElementById('fileStatsChart').getContext('2d');
        var fileStatsChart = new Chart(ctx1, {
            type: 'line',
            data: {
                datasets: [{
                    label: 'Gesamtgröße (GB)',
                    data: fileStatsData.map(point => ({x: point.x, y: point.y})),
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0, 0, 255, 0.1)',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'blue',
                    pointHoverBackgroundColor: 'white',
                    pointBorderColor: 'blue',
                    pointHoverBorderColor: 'blue',
                    fill: false
                }, {
                    label: 'Maximale Größe (GB)',
                    data: maxFileSizeLineData, // Verwenden Sie die vorbereiteten Daten für die Linie der maximalen Größe
                    borderColor: 'green',
                    backgroundColor: 'rgba(0, 255, 0, 0.1)',
                    fill: false
                }],
                labels: fileStatsData.map(point => point.label)
            },
            options: {
                scales: {
                    xAxes: [{
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'YYYY-MM-DD',
                            displayFormats: {
                                day: 'YYYY-MM-DD'
                            }
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Datum'
                        },
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Größe (GB)'
                        },
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        fontColor: 'black'
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                }
            }
        });

        // Daten für das Koordinatensystem der Dateianzahl
        var fileCountData = <?php echo json_encode($dataPointsFileStats); ?>;

        // Koordinatensystem für die Dateianzahl
        var ctx2 = document.getElementById('fileCountChart').getContext('2d');
        var fileCountChart = new Chart
(ctx2, {
type: 'line',
data: {
datasets: [{
label: 'Dateianzahl in Files',
data: fileCountData.map(point => ({x: point.x, y: point.fileCount})),
borderColor: 'red',
backgroundColor: 'rgba(255, 0, 0, 0.1)',
pointRadius: 5,
pointHoverRadius: 7,
pointBackgroundColor: 'red',
pointHoverBackgroundColor: 'white',
pointBorderColor: 'red',
pointHoverBorderColor: 'red',
fill: false
}],
labels: fileCountData.map(point => point.label)
},
options: {
scales: {
xAxes: [{
type: 'time',
time: {
unit: 'day',
tooltipFormat: 'YYYY-MM-DD',
displayFormats: {
day: 'YYYY-MM-DD'
}
},
scaleLabel: {
display: true,
labelString: 'Datum'
},
ticks: {
autoSkip: true,
maxTicksLimit: 10
}
}],
yAxes: [{
scaleLabel: {
display: true,
labelString: 'Dateianzahl'
},
ticks: {
stepSize: 1,
precision: 0
}
}]
},
legend: {
display: true,
position: 'top',
labels: {
fontColor: 'black'
}
},
tooltips: {
mode: 'index',
intersect: false
}
}
});
</script>
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