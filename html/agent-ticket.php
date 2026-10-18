<?php
// ============================================================
// agent-ticket.php   —   view one ticket as an agent
// ============================================================
// Shows a single ticket. The agent can mark an open ticket
// as Resolved (with the resolution notes). Agents can see
// every ticket — there is no user ownership check here.
// ============================================================

session_start();
require_once 'db.php';

// Must be logged in as an agent first.
if (!isset($_SESSION['agent_id'])) {
    header('Location: agent-login.php');
    exit;
}

$ticket_id = $_GET['id'] ?? 0;

// Fetch this ticket.
// We also join the agents table so we can show which agent resolved it.
$search = $pdo->prepare('
    SELECT t.*, a.full_name AS resolved_by
    FROM tickets t
    LEFT JOIN agents a ON a.agent_id = t.agent_id
    WHERE t.ticket_id = ?
');
$search->execute([$ticket_id]);
$ticket = $search->fetch();

if (!$ticket) {
    flash('Ticket not found.');
    header('Location: agent-dashboard.php');
    exit;
}

// ---- Resolve or Elevate the ticket --------------------------
// Two actions, both only allowed while the ticket is still open:
//   resolve  -> status becomes 'resolved' (agent has the answer)
//   elevate  -> hands the ticket to a more senior agent,
//               status stays 'open'
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ticket['status'] === 'open') {
    $action = $_POST['action'] ?? '';

    // Action 1: RESOLVE
    // Sets status to 'resolved', records the resolution text and
    // which agent did it.
    if ($action === 'resolve') {
        $notes = trim($_POST['resolution_notes'] ?? '');

        if ($notes === '') {
            $error = 'Please write the resolution details.';
        } else {
            $update = $pdo->prepare(
                "UPDATE tickets
                 SET status = 'resolved',
                     resolved_at = NOW(),
                     resolution_notes = ?,
                     agent_id = ?
                 WHERE ticket_id = ?"
            );
            $update->execute([$notes, $_SESSION['agent_id'], $ticket_id]);

            flash('Ticket #' . $ticket_id . ' resolved.');
            header('Location: agent-ticket.php?id=' . $ticket_id);
            exit;
        }
    }

    // Action 2: ELEVATE
    // Only a junior agent (level 2) may elevate, and only to the
    // senior agent (level 1). The ticket keeps status 'open' but
    // its agent_id changes to the senior agent, and we record who
    // it was handed up to in elevated_to.
    if ($action === 'elevate') {
        if ((int) $_SESSION['support_level'] !== 2) {
            $error = 'Only a junior agent (level 2) can elevate a ticket.';
        } else {
            // Find the level-1 senior agent.
            $senior = $pdo->query('SELECT agent_id FROM agents WHERE support_level = 1 LIMIT 1')->fetch();

            if (!$senior) {
                $error = 'No senior agent found to elevate to.';
            } else {
                $senior_id = $senior['agent_id'];

                // Re-assign the ticket to the senior agent and record
                // the elevation. status stays 'open'.
                $pdo->prepare(
                    'UPDATE tickets
                     SET agent_id = ?, elevated_to = ?
                     WHERE ticket_id = ? AND status = "open"'
                )->execute([$senior_id, $senior_id, $ticket_id]);

                flash('Ticket #' . $ticket_id . ' elevated to the senior agent.');
                header('Location: agent-ticket.php?id=' . $ticket_id);
                exit;
            }
        }
    }
}

$page_title = 'Agent Ticket';
require 'header.php';
?>
<h1>Ticket #<?= $ticket['ticket_id'] ?> <?= status_badge($ticket['status']) ?></h1>

<?php if ($error !== null): ?>
    <p class="flash-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<table>
    <tr><th>Status</th><td><?= status_badge($ticket['status']) ?></td></tr>
    <tr><th>Subject</th><td><?= htmlspecialchars($ticket['subject']) ?></td></tr>
    <tr><th>Submitted</th><td><?= $ticket['submitted_at'] ?></td></tr>
    <!-- htmlspecialchars: show text as plain text (no HTML).
         nl2br: keep the line breaks the user typed. -->
    <tr><th>Description</th><td><?= nl2br(htmlspecialchars((string) $ticket['description'])) ?></td></tr>
</table>

<?php if ($ticket['status'] === 'open'): ?>
    <h2>Resolve this ticket</h2>
    <form method="post">
        <input type="hidden" name="action" value="resolve">
        <p>
            <label for="resolution_notes">Resolution details</label><br>
            <textarea id="resolution_notes" name="resolution_notes" rows="4" required></textarea>
        </p>
        <p><button type="submit">Mark as Resolved</button></p>
    </form>

    <?php if ((int) $_SESSION['support_level'] === 2): ?>
        <!-- The Elevate form is only shown to junior agents (level 2) -->
        <h2>Or elevate this ticket</h2>
        <p>Pass this ticket to the senior agent. It stays open.</p>
        <form method="post">
            <input type="hidden" name="action" value="elevate">
            <p><button type="submit">Elevate to Senior Agent</button></p>
        </form>
    <?php endif; ?>
<?php else: ?>
    <p><strong>Resolved by:</strong> <?= htmlspecialchars((string) $ticket['resolved_by']) ?></p>
    <p><strong>Resolution:</strong></p>
    <p><?= nl2br(htmlspecialchars((string) $ticket['resolution_notes'])) ?></p>
<?php endif; ?>

<p><a href="agent-dashboard.php">&larr; Back to dashboard</a></p>
<?php require 'footer.php'; ?>