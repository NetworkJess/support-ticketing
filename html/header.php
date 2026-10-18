<?php
// ============================================================
// header.php   —   the top of every page (nav bar + page start)
// ============================================================
// Every page includes this file (with require_once) so we do not
// have to copy the HTML for the top of the page around.
//
// It uses the $page_title variable, set by the page like this:
//     $page_title = 'Home';
//
// IMPORTANT: include this line AFTER the page's "logic" section.
// header.php prints HTML immediately, and once PHP has printed
// anything it can no longer send a redirect (header('Location:')).
// So each page does its checks/redirects FIRST, then loads
// header.php to draw the page.
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page_title ?> · Support Ticketing</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">🎫 Support Ticketing</a>
    <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Hi, <?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="dashboard.php">My Tickets</a>
            <a href="new-ticket.php">Open Ticket</a>
            <a href="logout.php">Logout</a>
        <?php elseif (isset($_SESSION['agent_id'])): ?>
            <span>Agent: <?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="agent-dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">User Login</a>
            <a href="agent-login.php">Agent Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<?php if (isset($_SESSION['flash'])): ?>
    <!-- Show the saved flash message, then remove it from the session -->
    <div class="flash"><?= htmlspecialchars($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<main>
