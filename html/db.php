<?php
// ============================================================
// db.php   —   the database connection + small helpers
// ============================================================
// This file is used by every other page. It connects to MySQL
// and gives us a variable called $pdo that we can use to run
// queries.
//
// We "include" this file at the top of every page like this:
//     require_once 'db.php';
// ============================================================

// The login details come from environment variables set by the
// web container in compose.yaml. getenv() reads a container variable,
// and the fallback means "use this default if it is not set" — so the
// app still runs even if someone has not configured them.
$db_name = getenv('MYSQL_DATABASE') ?: 'Ticketing';
$db_user = getenv('MYSQL_USER') ?: 'Jess';
$db_pass = getenv('MYSQL_PASSWORD') ?: 'admin';

$pdo = new PDO(
    'mysql:host=db;port=3306;dbname=' . $db_name . ';charset=utf8mb4',   // where the database is
    $db_user,   // database username (see compose.yaml)
    $db_pass,   // database password (see compose.yaml)
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // if a query fails, PHP should show us the error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // return rows as name => value arrays
    ]
);

// ----------------------------------------------------------
// flash(text) — save a one-time message to show on the next page.
// For example after saving a ticket: flash('Ticket created.');
// The message is shown at the top of the next page by header.php,
// then removed so it does not keep showing.
// ----------------------------------------------------------
function flash(string $text): void {
    $_SESSION['flash'] = $text;
}

// ----------------------------------------------------------
// status_badge(status) — return a coloured badge for a ticket
// status, so Open / Resolved / Closed are easy to spot at a
// glance. Used on the My Tickets list and ticket detail page.
// ----------------------------------------------------------
function status_badge(string $status): string {
    return '<span class="badge badge-' . $status . '">' . $status . '</span>';
}