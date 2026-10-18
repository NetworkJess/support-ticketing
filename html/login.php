<?php
// ============================================================
// login.php   —   log in as a normal user
// ============================================================
// 1. If the form was submitted: find the user and check the password.
// 2. If it matches: remember who they are in the session, then
//    redirect to their dashboard.
// 3. Otherwise show the login form again with an error message.
//
// NOTE: the redirect (header('Location:')) happens BEFORE
// header.php is loaded — once a page has printed HTML it can no
// longer redirect.
// ============================================================

session_start();
require_once 'db.php';          // gives us $pdo and flash()

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Find this user (if they exist) by username.
    $search = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $search->execute([$username]);
    $user = $search->fetch();

    // Check the password matches.
    if ($user && $user['password'] === $password) {
        // Remember who is logged in. $_SESSION carries over to
        // the next page because of session_start().
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];

        // Redirect BEFORE printing anything.
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

// ---- from here down the page is printed ----
$page_title = 'User Login';
require 'header.php';
?>
<h1>User Login</h1>

<?php if ($error !== null): ?>
    <p class="flash-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="auth-form">
<form method="post">
    <p>
        <label for="username">Username</label><br>
        <input type="text" id="username" name="username" required autofocus>
    </p>
    <p>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required>
    </p>
    <p><button type="submit">Login</button></p>
</form>
</div>

<p>New here? <a href="register.php">Create an account</a>.</p>
<?php require 'footer.php'; ?>