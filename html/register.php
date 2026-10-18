<?php
// ============================================================
// register.php   —   create a new user account
// ============================================================
// Shows a form. When the form is submitted (POST), we:
//   1. check every field is filled in
//   2. check the username is not already taken
//   3. save the new user in the database
//   4. send them to the login page
// ============================================================

session_start();
require_once 'db.php';

$error = null;   // used to show a message if something goes wrong

if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // was the form submitted?
    // Get the values the user typed.
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';

    if ($full_name === '' || $username === '' || $password === '') {
        $error = 'All fields are required.';
    } else {
        // Check if this username is already in the database.
        $check = $pdo->prepare('SELECT user_id FROM users WHERE username = ?');
        $check->execute([$username]);
        $existing = $check->fetch();

        if ($existing) {
            $error = 'That username is already taken.';
        } else {
            // Save the new user. '?' marks are placeholders that PDO
            // fills in safely for us (this stops people breaking the query).
            $add = $pdo->prepare('INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)');
            $add->execute([$username, $password, $full_name]);

            flash('Account created! You can now log in.');   // show this on the login page
            header('Location: login.php');
            exit;
        }
    }
}

$page_title = 'Register';
require 'header.php';
?>
<h1>Register</h1>

<?php if ($error !== null): ?>
    <p class="flash-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="auth-form">
<form method="post">
    <p>
        <label for="full_name">Full name</label><br>
        <input type="text" id="full_name" name="full_name" required>
    </p>
    <p>
        <label for="username">Username</label><br>
        <input type="text" id="username" name="username" required>
    </p>
    <p>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required>
    </p>
    <p><button type="submit">Register</button></p>
</form>
</div>

<p>Already have an account? <a href="login.php">Log in</a>.</p>
<?php require 'footer.php'; ?>