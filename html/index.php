<?php
// ============================================================
// index.php   —   the home page
// ============================================================
// Just a welcome page with two links:
//   - User Login  -> for normal users (they can register from the
//                    login page or the nav bar)
//   - Agent Login -> for support agents
// ============================================================

session_start();          // start the session (needed for flash messages & logins)
$page_title = 'Home';     // used by header.php
require 'header.php';     // print the top of the page (nav bar etc.)
?>
<div class="hero">
    <h1>Welcome to the Support Ticketing System</h1>
    <p>Report an issue as a user, or manage tickets as a support agent.</p>

    <p class="hero-actions">
        <a class="btn" href="login.php">User Login</a>
        <a class="btn" href="agent-login.php">Agent Login</a>
    </p>
</div>
<?php require 'footer.php'; ?>