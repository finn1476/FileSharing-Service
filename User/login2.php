<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        die("Username and password cannot be empty.");
    }

    // Path to the .htpasswd file
    $htpasswd_file = __DIR__ . '/password/.htpasswd';

    // Check if the .htpasswd file exists
    if (!file_exists($htpasswd_file)) {
        die("No users found.");
    }

    // Read the .htpasswd file
    $lines = file($htpasswd_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        list($stored_username, $stored_hashed_password) = explode(':', $line);

        if ($username === $stored_username) {
            if (password_verify($password, $stored_hashed_password)) {
                $_SESSION['username'] = $username;
                header("Refresh: 5; url=index.php");
                die("<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Login Successful</title>
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

      main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        max-width: 100%;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      p {
        margin-bottom: 10px;
      }
    </style>
</head>
<body>
    <main>
        <div class='awasr'>
            <h2>Login Successful!</h2>
            <p>You will be redirected in 5 seconds...</p>
        </div>
    </main>
</body>
</html>");
            } else {
                die("<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Incorrect Password</title>
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

      main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        max-width: 100%;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      p {
        margin-bottom: 10px;
      }
    </style>
</head>
<body>
    <main>
        <div class='awasr'>
            <h2>Incorrect Password!</h2>
            <p>Please check your credentials.</p>
        </div>
    </main>
</body>
</html>");
            }
        }
    }

    die("<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Username Not Found</title>
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

      main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        max-width: 100%;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      p {
        margin-bottom: 10px;
      }
    </style>
</head>
<body>
    <main>
        <div class='awasr'>
            <h2>Username Not Found!</h2>
            <p>Please check your credentials.</p>
        </div>
    </main>
</body>
</html>");
} else {
    die("Invalid request.");
}
?>
