<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Registrierung und Anmeldung</title>

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
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        max-width: 100%;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      .maske {
        margin-bottom: 20px;
      }

      .abusetextwidth {
        text-align: left;
        margin-bottom: 20px;
      }

      .emailbutton {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        background-color: var(--button-color);
        color: white;
        border-radius: 5px;
        margin: 5px;
        transition: background-color 0.3s ease;
      }

      .emailbutton:hover {
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
	    input[type="text"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: var(--button-color);
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: var(--button-hover-color);
        }

        .hidden {
            display: none;
        }

        .deactivated-message {
            background-color: var(--error-color);
            color: white;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">Anonfile</div>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../pricing.php">Pricing</a>
        <a href="login.php">Login</a>
    </nav>
</header>
    <main>
        <div class="awasr">
            <h2>Registrierung</h2>
            <!-- Füge der Registrierungsform Klasse 'hidden' hinzu -->
            <form action="register.php" method="post" class="registration-form hidden">
                <input type="hidden" name="register" value="true">
                <input type="submit" value="Jetzt registrieren">
            </form>

            <h2>Anmeldung</h2>
            <!-- Füge der Anmeldeform Klasse 'hidden' hinzu -->
            <form action="login2.php" method="post" class="login-form hidden">
                <label for="username">Benutzername:</label>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Passwort:</label>
                <input type="password" id="password" name="password" required><br><br>
                <input type="submit" value="Anmelden">
            </form>

            <!-- Nachricht für deaktivierte Registrierung und Anmeldung -->
            <div class="deactivated-message hidden">
                <h2>Registration and login deactivated</h2>
                <p>Registration and login are currently disabled. Please try again later.</p>
            </div>
        </div>
    </main>

<footer class="footer">
    <div class="footer-links">
      <a href="../FAQ.php">FAQ</a>
      <a href="../impressum.php">Imprint</a>
      <a href="../abuse.php">Abuse</a>
      <a href="../terms.php">ToS</a>
      <a href="../datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
</footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var csvFile = '../Speicher/userportal.csv';

            var request = new XMLHttpRequest();
            request.open('GET', csvFile, true);
            request.onreadystatechange = function() {
                if (request.readyState === XMLHttpRequest.DONE && request.status === 200) {
                    var csvData = request.responseText;

                    if (csvData.includes('1')) {
                        document.querySelectorAll('.registration-form, .login-form').forEach(function(form) {
                            form.classList.remove('hidden');
                        });
                    } else {
                        document.querySelector('.deactivated-message').classList.remove('hidden');
                        document.querySelectorAll('.registration-form, .login-form').forEach(function(form) {
                            form.classList.add('hidden');
                        });
                    }
                }
            };
            request.send();
        });
    </script>
</body>
</html>
