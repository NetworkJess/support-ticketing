<?php
// ============================================================
// dashboard.php   —   "My Tickets" for a logged in user
// ============================================================
// Shows all the tickets the current user has opened,
// newest first, each with its current status.
// Only visible if a user is logged in (see the check below).
// ============================================================

session_start();
require_once 'db.php';

// If nobody is logged in, send them to the login page.
// (This redirect happens before anything is printed.)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get only this user's tickets, newest first.
$tickets = $pdo->query(
    'SELECT * FROM tickets
     WHERE user_id = ' . $_SESSION['user_id'] . '
     ORDER BY submitted_at DESC'
)->fetchAll();

$page_title = 'My Tickets';
require 'header.php';
?>
<h1>My Tickets</h1>

<p><a class="btn" href="new-ticket.php">Open a new ticket</a></p>

<?php if (count($tickets) === 0): ?>
    <p>You have not opened any tickets yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Submitted</th>
            <th></th>
        </tr>
        <?php foreach ($tickets as $t): ?>
            <tr>
                <td>#<?= $t['ticket_id'] ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= status_badge($t['status']) ?></td>
                <td><?= $t['submitted_at'] ?></td>
                <td><a href="ticket.php?id=<?= $t['ticket_id'] ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php require 'footer.php'; ?>