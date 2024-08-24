<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>404 - Page Not Found</title>
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
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
      }

      .awasr {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        width: 100%;
        border: 1px solid var(--border-color);
      }

      h1 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      form {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      label {
        font-weight: bold;
        margin-bottom: 5px;
      }

      input,
      textarea,
      select {
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        margin-top: 5px;
        width: 100%;
      }

      button {
        padding: 10px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s ease;
      }

      button:hover {
        background-color: var(--button-hover-color);
      }

      a {
        color: var(--primary-color);
        text-decoration: none;
      }

      a:hover {
        color: var(--accent-color);
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
	  h2{
		  color:red;
	  }
    </style>
</head>
<body>
<header>
    <div class="logo">Anonfile</div>
<?php

include("templates/header.php");	
	
?>
</header>

    <main>
        <div class="awasr">
            <h2>404 - Page Not Found</h2>
            <p>The page you requested could not be found.</p>
            <a class="emailbutton" href="https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST']); ?>/index.php">Back to Home</a>
        </div>
    </main>

  <footer class="footer">
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
