<?php
// ============================================================
// ticket.php   —   view one of MY tickets
// ============================================================
// Shows the details of a single ticket. Because it uses
// "WHERE ticket_id = ? AND user_id = ?" a user can only see
// their OWN tickets — someone else's ID will show "not found".
//
// If the ticket is Resolved, the owner can close it here.
// ============================================================

session_start();
require_once 'db.php';

// Must be logged in first.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// The ticket ID comes from the address bar: ticket.php?id=4
$ticket_id = $_GET['id'] ?? 0;

// Get the ticket plus the name of the agent who resolved it (if any).
$search = $pdo->prepare('
    SELECT t.*, a.full_name AS resolved_by
    FROM tickets t
    LEFT JOIN agents a ON a.agent_id = t.agent_id
    WHERE t.ticket_id = ? AND t.user_id = ?
');
$search->execute([$ticket_id, $_SESSION['user_id']]);
$ticket = $search->fetch();

if (!$ticket) {
    flash('Ticket not found.');
    header('Location: dashboard.php');
    exit;
}

// ---- Close the ticket ------------------------------------------
// Only possible when an agent has marked it Resolved.
$close_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($ticket['status'] === 'resolved') {
        $pdo->query(
            "UPDATE tickets SET status = 'closed', closed_at = NOW()
             WHERE ticket_id = " . $ticket_id
        );
        flash('Ticket #' . $ticket_id . ' closed.');
        header('Location: ticket.php?id=' . $ticket_id);
        exit;
    } else {
        $close_error = 'A ticket can only be closed after an agent has resolved it.';
    }
}

$page_title = 'Ticket Details';
require 'header.php';
?>
<h1>Ticket #<?= $ticket['ticket_id'] ?> <?= status_badge($ticket['status']) ?></h1>

<?php if ($close_error !== null): ?>
    <p class="flash-error"><?= htmlspecialchars($close_error) ?></p>
<?php endif; ?>

<table>
    <tr><th>Subject</th><td><?= htmlspecialchars($ticket['subject']) ?></td></tr>
    <tr><th>Submitted</th><td><?= $ticket['submitted_at'] ?></td></tr>
    <!-- htmlspecialchars: show text as plain text (no HTML).
         nl2br: keep the line breaks the user typed. -->
    <tr><th>Description</th><td><?= nl2br(htmlspecialchars($ticket['description'])) ?></td></tr>

    <?php if ($ticket['status'] == 'resolved' || $ticket['status'] == 'closed'): ?>
        <tr><th>Resolved by</th><td><?= htmlspecialchars((string) $ticket['resolved_by']) ?></td></tr>
        <tr><th>Resolution</th><td><?= nl2br(htmlspecialchars((string) $ticket['resolution_notes'])) ?></td></tr>
    <?php endif; ?>
</table>

<?php if ($ticket['status'] == 'resolved'): ?>
    <!-- Close button — only shown when the ticket is Resolved -->
    <form method="post" onsubmit="return confirm('Close this ticket?');">
        <button type="submit">Close Ticket</button>
    </form>
<?php endif; ?>

<p><a href="dashboard.php">&larr; Back to My Tickets</a></p>
<?php require 'footer.php'; ?>