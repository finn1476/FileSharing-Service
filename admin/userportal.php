<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Admin-Seite</title>
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

        h1 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            text-align: center;
        }

        .button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            background-color: var(--button-color);
            color: white;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: var(--button-hover-color);
        }

        .status {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        .active {
            color: green;
        }

        .inactive {
            color: red;
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
        <h1>Admin-Seite</h1>
        <p>Hier kannst du das Benutzerportal ein- oder ausschalten:</p>
        <button id="toggleButton" class="button">Benutzerportal ein/ausschalten</button>
        <div id="statusMessage" class="status"></div>
    </main>

    <footer class="footer">
        <div class="footer-links">
            <a href="adminpanel5.php">Statistiken</a>
            <a href="adminpanel4.php">Datei-Typen</a>
            <a href="adminpanel3.php">Benutzer-Verwaltung</a>
            <a href="adminpanel2.php">Upload-Grenze</a>
            <a href="admindelete.php">Löschen</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var status = 0; // Annahme: Portal ist standardmäßig deaktiviert

            var toggleButton = document.getElementById("toggleButton");
            var statusMessage = document.getElementById("statusMessage");

            function updateStatusMessage() {
                statusMessage.innerHTML = (status === 1) ? "<span class='active'>Aktiviert</span>" : "<span class='inactive'>Inaktiv</span>";
            }

            function loadStatusFromCSV() {
                var csvFile = '../Speicher/userportal.csv';

                var request = new XMLHttpRequest();
                request.open('GET', csvFile, true);
                request.onreadystatechange = function() {
                    if (request.readyState === XMLHttpRequest.DONE && request.status === 200) {
                        var csvData = request.responseText;
                        status = (csvData.includes('1')) ? 1 : 0;
                        updateStatusMessage();
                    }
                };
                request.send();
            }

            loadStatusFromCSV();

            function saveStatusToCSV() {
                var csvFile = '../Speicher/userportal.csv';
                var csvData = (status === 1) ? '1' : '0';

                var request = new XMLHttpRequest();
                request.open('POST', 'save_status.php', true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                request.onreadystatechange = function() {
                    if (request.readyState === XMLHttpRequest.DONE && request.status === 200) {
                        console.log('Status erfolgreich gespeichert');
                    }
                };
                request.send('status=' + csvData);
            }

            toggleButton.addEventListener("click", function() {
                status = 1 - status;
                var message = (status === 1) ? "Benutzerportal wurde aktiviert!" : "Benutzerportal wurde deaktiviert!";
                alert(message);
                updateStatusMessage();
                saveStatusToCSV();
            });
        });
    </script>
</body>
</html>
