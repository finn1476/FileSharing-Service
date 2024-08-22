<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
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

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        background-color: var(--background-color);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .awasr h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .awasr img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
      }

      .awasr form {
        margin-bottom: 20px;
      }

      .awasr label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
      }

      .awasr input[type="text"], 
      .awasr input[type="password"] {
        width: calc(100% - 20px);
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        box-sizing: border-box;
      }

      .awasr input[type="submit"], 
      .awasr button {
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
      }

      .awasr input[type="submit"]:hover, 
      .awasr button:hover {
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
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
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

      .buttona {
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        display: inline-block;
        margin: 10px 5px;
      }

      .buttona:hover {
        background-color: var(--button-hover-color);
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
    <script>
      function validateChangePasswordForm() {
        var newPassword1 = document.getElementById("newPassword1").value;
        var newPassword2 = document.getElementById("newPassword2").value;
        if (newPassword1 !== newPassword2) {
          alert("Die neuen Passwörter stimmen nicht überein.");
          return false;
        }
        return true;
      }

      function validateAddUserForm() {
        var newPassword = document.getElementById("newPassword").value;
        var confirmNewPassword = document.getElementById("confirmNewPassword").value;
        if (newPassword !== confirmNewPassword) {
          alert("Die Passwörter stimmen nicht überein.");
          return false;
        }
        return true;
      }

      function confirmDeleteUser() {
        return confirm("Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?");
      }
    </script>
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
            <div>
                <h2>Anonymer File Upload</h2>
            </div>
            
            <h1>Admin Panel</h1>

            <?php
            if (isset($_SERVER['REMOTE_USER'])) {
                $loggedInUser = $_SERVER['REMOTE_USER'];
                echo "<p>Eingeloggt als: $loggedInUser</p>";

                if ($loggedInUser === 'admin') {
                    echo "<form action='admin.php' method='post' onsubmit='return validateAddUserForm()'>";
                    echo "<label for='newUsername'>New Username:</label>";
                    echo "<input type='text' id='newUsername' name='newUsername' required>";
                    echo "<label for='newPassword'>Password:</label>";
                    echo "<input type='password' id='newPassword' name='newPassword' required>";
                    echo "<label for='confirmNewPassword'>Confirm Password:</label>";
                    echo "<input type='password' id='confirmNewPassword' name='confirmNewPassword' required>";
                    echo "<input type='submit' name='addUser' value='Add User'>";
                    echo "</form>";

                    echo "<form action='admin.php' method='post' onsubmit='return validateChangePasswordForm()'>";
                    echo "<label for='username'>Username:</label>";
                    echo "<input type='text' id='username' name='username' value='$loggedInUser' required>";
                    echo "<label for='newPassword1'>New Password:</label>";
                    echo "<input type='password' id='newPassword1' name='newPassword1' required>";
                    echo "<label for='newPassword2'>Confirm New Password:</label>";
                    echo "<input type='password' id='newPassword2' name='newPassword2' required>";
                    echo "<input type='submit' name='updatePassword' value='Change Password'>";
                    echo "</form>";

                    echo "<form action='admin.php' method='post' onsubmit='return confirmDeleteUser()'>";
                    echo "<label for='usernameToDelete'>Username to Delete:</label>";
                    echo "<input type='text' id='usernameToDelete' name='usernameToDelete' required>";
                    echo "<input type='submit' name='deleteUser' value='Delete User'>";
                    echo "</form>";

                    echo "<form action='show_users.php' method='post'>
                            <button class='buttona' type='submit' name='showUsers'>Show Existing Users</button>
                          </form>";
                } else {
                    echo "<form action='admin.php' method='post' onsubmit='return validateChangePasswordForm()'>";
                    echo "<label for='newPassword1'>New Password:</label>";
                    echo "<input type='password' id='newPassword1' name='newPassword1' required>";
                    echo "<label for='newPassword2'>Confirm New Password:</label>";
                    echo "<input type='password' id='newPassword2' name='newPassword2' required>";
                    echo "<input type='hidden' id='username' name='username' value='$loggedInUser'>";
                    echo "<input type='submit' name='updatePassword' value='Change Password'>";
                    echo "</form>";
                }
            } else {
                echo "<p>Du bist nicht angemeldet.</p>";
            }
            ?>
            <p>
                <a class="buttona" href="adminpanel2.php">Zurück</a>
                <a class="buttona" href="adminpanel4.php">Nächste Seite</a>
            </p>
            <p><a class="buttona" href="index.php">HOME</a></p>
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
