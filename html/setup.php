<?php
// ============================================================
// setup.php   —   create the tables and add sample data
// ============================================================
// Run this ONCE by opening:  http://localhost:8000/setup.php
//
// It does two things:
//   1. Creates the 3 database tables if they don't exist yet
//      (agents, users, tickets).
//   2. Adds some sample accounts and tickets so the site is
//      not empty when you open it.
//
// It is SAFE to run again: "IF NOT EXISTS" and "INSERT IGNORE"
// mean it will never overwrite or delete anything.
// ============================================================

require 'db.php';             // get the database connection ($pdo)

// The text shown at the bottom of the page (Report / Error).
$message = 'Setup complete!';

// -------------------------------------------------------------
// PART 1 — CREATE THE TABLES
// -------------------------------------------------------------
// Each CREATE TABLE makes one table. "IF NOT EXISTS" means:
// "if a table with this name is already there, do nothing".
// -------------------------------------------------------------

// --- agents: the support team ---------------------------------
// support_level: 1 = senior (highest), 2 = junior
$pdo->query(
    'CREATE TABLE IF NOT EXISTS agents (
        agent_id      INT AUTO_INCREMENT PRIMARY KEY,
        username      VARCHAR(50)  NOT NULL UNIQUE,
        password      VARCHAR(255) NOT NULL,
        full_name     VARCHAR(100) NOT NULL,
        support_level TINYINT      NOT NULL DEFAULT 2
    )'
);

// --- users: the people who report problems --------------------
$pdo->query(
    'CREATE TABLE IF NOT EXISTS users (
        user_id   INT AUTO_INCREMENT PRIMARY KEY,
        username  VARCHAR(50)  NOT NULL UNIQUE,
        password  VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL
    )'
);

// --- tickets: the problems that get reported ------------------
// status can be: open  -> resolved  -> closed
// submitted_at gets the current time automatically (DEFAULT).
$pdo->query(
    'CREATE TABLE IF NOT EXISTS tickets (
        ticket_id    INT AUTO_INCREMENT PRIMARY KEY,
        user_id      INT          NOT NULL,
        subject      VARCHAR(150) NOT NULL,
        description  TEXT         NOT NULL,
        status       ENUM("open","resolved","closed") NOT NULL DEFAULT "open",
        submitted_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        resolved_at  DATETIME,
        closed_at    DATETIME,
        resolution_notes TEXT,
        agent_id     INT,
        elevated_to  INT
    )'
);

// -------------------------------------------------------------
// PART 2 — ADD SAMPLE DATA
// -------------------------------------------------------------
// We count how many rows are already in each table. If it is
// zero (first run), we add the demo accounts and tickets.
// This way setup.php does not duplicate data if you re-run it.
// -------------------------------------------------------------

if ($pdo->query('SELECT COUNT(*) AS n FROM agents')->fetch()['n'] === 0) {
    $pdo->query(
        "INSERT INTO agents (username, password, full_name, support_level) VALUES
         ('alice', 'password123', 'Alice Manager',        1),
         ('bob',   'password123', 'Bob Technician',       2)"
    );
}

if ($pdo->query('SELECT COUNT(*) AS n FROM users')->fetch()['n'] === 0) {
    $pdo->query(
        "INSERT INTO users (username, password, full_name) VALUES
         ('carol', 'password123', 'Carol User'),
         ('dave',  'password123', 'Dave User')"
    );
}

if ($pdo->query('SELECT COUNT(*) AS n FROM tickets')->fetch()['n'] === 0) {
    $pdo->query(
        "INSERT INTO tickets
         (user_id, subject, description, status, agent_id, elevated_to, resolution_notes, submitted_at, resolved_at, closed_at) VALUES
         (1, 'Cannot log in',       'My account says invalid password even though I typed the right one.',                    'open',      NULL, NULL, NULL, NOW() - INTERVAL 2 DAY, NULL, NULL),
         (1, 'Printer not working', 'The office printer is offline since the update.',                                       'resolved',  1,   NULL, 'Told the user to restart the print spooler; fixed.', NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 4 DAY, NULL),
         (2, 'Email sync issue',    'Outlook keeps asking for my password every time I open it.',                            'open',      1,   1,    NULL, NOW() - INTERVAL 1 DAY, NULL, NULL),
         (1, 'Cannot reach VPN',    'I can log in but the VPN connection drops after a few minutes.',                        'open',      NULL, NULL, NULL, NOW() - INTERVAL 10 HOUR, NULL, NULL),
         (2, 'Laptop battery drain','My work laptop goes from full to empty in under two hours.',                             'resolved',  2,   NULL, 'Replaced the faulty battery under warranty.', NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 5 DAY, NULL),
         (1, 'Password reset',      'I requested a password reset email but it never arrived.',                              'closed',    2,   NULL, 'Reset link was marked as spam; sent a new one directly.', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 7 DAY, NOW() - INTERVAL 7 DAY),
         (2, 'Cannot install Teams','The Teams installer fails with a generic error halfway through.',                       'closed',    1,   NULL, 'Cleared the cached installer and re-ran; worked.', NOW() - INTERVAL 9 DAY, NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY)"
    );
}
?>
<h1>Setup</h1>
<p><?= $message ?></p>

<p>Demo logins (all passwords are <strong>password123</strong>):</p>
<table>
    <tr><th>Role</th><th>Username</th><th>Password</th></tr>
    <tr><td>Agent</td><td>alice</td><td>password123</td></tr>
    <tr><td>Agent</td><td>bob</td><td>password123</td></tr>
    <tr><td>User</td><td>carol</td><td>password123</td></tr>
    <tr><td>User</td><td>dave</td><td>password123</td></tr>
</table>

<p><a href="index.php">Go to the app</a></p>