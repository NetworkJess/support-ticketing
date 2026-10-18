<?php
// ============================================================
// agent-login.php   —   log in as a support agent
// ============================================================
// This is exactly the same as login.php but for the agents
// table instead of the users table.
// ============================================================

session_start();
require_once 'db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $search = $pdo->prepare('SELECT * FROM agents WHERE username = ?');
    $search->execute([$username]);
    $agent = $search->fetch();

    if ($agent && $agent['password'] === $password) {
        $_SESSION['agent_id']       = $agent['agent_id'];
        $_SESSION['full_name']      = $agent['full_name'];
        $_SESSION['support_level']  = $agent['support_level'];   // 1 = senior, 2 = junior

        header('Location: agent-dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

$page_title = 'Support Agent Login';
require 'header.php';
?>
<h1>Support Agent Login</h1>

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
<?php require 'footer.php'; ?>