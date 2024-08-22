<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Terms of Service</title>
    <style>
        :root {
            --primary-color: #005f73;
            --secondary-color: #94d2bd;
            --accent-color: #ee9b00;
            --background-color: #f7f9fb;
            --text-color: #023047;
            --border-color: #d9e2ec;
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
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        ul {
            list-style-type: disc;
            margin: 20px 0;
            padding-left: 20px;
        }

        li {
            margin-bottom: 15px;
            line-height: 1.6;
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
    </style>
</head>
<body>
    <header>
        <div class="logo">Anonfile</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="pricing.php">Pricing</a>
            <a href="User/login.php">Login</a>
        </nav>
    </header>

    <main>
        <h2>Terms of Service</h2>
        <ul>
            <li>Do not upload anything that is illegal under German law or that you do not own.</li>
            <li>We believe in privacy and do not log your activity.</li>
            <li>We do not accept responsibility for any downtime or loss of data. Users are advised to take appropriate measures, such as regular backups, to safeguard their data and acknowledge that unforeseen technical issues may result in temporary service interruptions.</li>
            <li>The terms of use are subject to change, and it is the user's responsibility to stay informed about any updates. Users are encouraged to regularly review the terms and conditions to ensure compliance with the latest policies.</li>
        </ul>
    </main>

    <footer>
        <div class="footer-links">
            <a href="FAQ.php">FAQ</a>
            <a href="impressum.php">Imprint</a>
            <a href="abuse.php">Abuse</a>
            <a href="terms.php">ToS</a>
            <a href="datenschutz.php">Privacy Policy</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
</body>
</html>
