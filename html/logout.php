<?php
// ============================================================
// logout.php   —   log out (works for users AND agents)
// ============================================================
// Deletes everything stored in the session (so nobody is
// logged in anymore) and returns to the home page.
// ============================================================

session_start();
require_once 'db.php';                   // gives us flash()

$_SESSION = [];                          // wipe the session (forget logins)
flash('You have been logged out.');      // tell them on the home page
header('Location: index.php');
exit;