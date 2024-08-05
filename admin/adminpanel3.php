<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
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
    <main>
        <div class="awasr">
            <div><h2>Anonymer File Upload</h2><br></div>
            <div class="maske"><img src="../bilder/vendetta-g41f352c32_1280-modified.png" alt="Guy Fawkes Mask" class="pictureguy"/></div>
            <h1>Admin Panel</h1>

            <?php
            // Überprüfen, ob der Benutzer angemeldet ist und den Benutzernamen anzeigen
            if (isset($_SERVER['REMOTE_USER'])) {
                $loggedInUser = $_SERVER['REMOTE_USER'];
                echo "<p>Eingeloggt als: $loggedInUser</p>";

                // Überprüfen, ob der Benutzer ein Administrator ist
                if ($loggedInUser === 'admin') {
                    // Admin-Funktionalität anzeigen
                    echo "<!-- Form to add a new user -->";
                    echo "<form action='admin.php' method='post' onsubmit='return validateAddUserForm()'>";
                    echo "<label for='newUsername'>New Username:</label><br>";
                    echo "<input type='text' id='newUsername' name='newUsername' required><br>";
                    echo "<label for='newPassword'>Password:</label><br>";
                    echo "<input type='password' id='newPassword' name='newPassword' required><br>";
                    echo "<label for='confirmNewPassword'>Confirm Password:</label><br>";
                    echo "<input type='password' id='confirmNewPassword' name='confirmNewPassword' required><br>";
                    echo "<input type='submit' name='addUser' value='Add User'>";
                    echo "</form>";

                    echo "<!-- Form to change the password -->";
                    echo "<form action='admin.php' method='post' onsubmit='return validateChangePasswordForm()'>";
                    echo "<label for='username'>Username:</label><br>";
                    echo "<input type='text' id='username' name='username' value='$loggedInUser' required><br>";
                    echo "<label for='newPassword1'>New Password:</label><br>";
                    echo "<input type='password' id='newPassword1' name='newPassword1' required><br>";
                    echo "<label for='newPassword2'>Confirm New Password:</label><br>";
                    echo "<input type='password' id='newPassword2' name='newPassword2' required><br>";
                    echo "<input type='submit' name='updatePassword' value='Change Password'>";
                    echo "</form>";

                    // Formular zum Löschen eines Benutzers anzeigen
                    echo "<!-- Form to delete a user -->";
                    echo "<form action='admin.php' method='post' onsubmit='return confirmDeleteUser()'>";
                    echo "<label for='usernameToDelete'>Username to Delete:</label><br>";
                    echo "<input type='text' id='usernameToDelete' name='usernameToDelete' required><br>";
                    echo "<input type='submit' name='deleteUser' value='Delete User'>";
                    echo "</form>";

                    echo"<form action='show_users.php' method='post'>
                <button class='buttona' type='submit' name='showUsers'>Show Existing Users</button>
            </form>";
                } else {
                    // Normale Benutzer-Funktionalität anzeigen
                    echo "<!-- Form to change the password -->";
                    echo "<form action='admin.php' method='post' onsubmit='return validateChangePasswordForm()'>";
                    echo "<label for='newPassword1'>New Password:</label><br>";
                    echo "<input type='password' id='newPassword1' name='newPassword1' required><br>";
                    echo "<label for='newPassword2'>Confirm New Password:</label><br>";
                    echo "<input type='password' id='newPassword2' name='newPassword2' required><br>";
                    echo "<input type='hidden' id='username' name='username' value='$loggedInUser'>";
                    echo "<input type='submit' name='updatePassword' value='Change Password'>";
                    echo "</form>";
                }
            } else {
                // Benutzer ist nicht angemeldet
                echo "<p>Du bist nicht angemeldet.</p>";
            }
            ?>
            <p><a class="buttona" href="adminpanel2.php">Zurück</a>
            <a class="buttona" href="adminpanel4.php">Nächste Seite</a></p>
            <a class="buttona" href="index.php">HOME</a></p>
            <!-- Ihre anderen Inhalte hier -->
        </div>
    </main>
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
