<?php
// ============================================================
// agent-dashboard.php   —   list of open tickets for agents
// ============================================================
// Shows all open tickets by default, newest first. Clicking
// "History" (?view=all) also shows resolved and closed tickets.
// The search box (optional) filters them by a word in the subject
// (LIKE means "contains").
// ============================================================

session_start();
require_once 'db.php';

// Must be logged in as an agent first.
if (!isset($_SESSION['agent_id'])) {
    header('Location: agent-login.php');
    exit;
}

// The search text (if any) comes from the URL: ?q=word
$search = trim($_GET['q'] ?? '');

// "Open" is the default view. "History" (?view=all) drops the status
// filter so agents can also see resolved and closed tickets.
$view = $_GET['view'] ?? 'open';

// Build the query. Open view only shows 'open' tickets; history view
// shows every ticket. If there is a search term we add a WHERE part
// that looks for it in the subject. LIKE '%...%' means "contains":
// typing "printer" finds "Printer not working".
$sql = 'SELECT t.*, u.full_name AS user_full_name
        FROM tickets t
        JOIN users u ON u.user_id = t.user_id
        WHERE 1 = 1';

if ($view !== 'all') {
    $sql .= ' AND t.status = "open"';
}

if ($search !== '') {
    // Escape % and _ (LIKE wildcards) so they are treated as normal text.
    $safe = str_replace(['%', '_'], ['\\%', '\\_'], $search);
    $sql .= ' AND t.subject LIKE "%' . $safe . '%"';
}
$sql .= ' ORDER BY t.submitted_at DESC';

// Run whichever query we built.
$tickets = $pdo->query($sql)->fetchAll();

$page_title = 'Support Dashboard';
require 'header.php';
?>
<h1>Support Dashboard</h1>

<!-- Search form: sends ?q=... to this same page (method=get) -->
<form method="get">
    <!-- Keep the current view when searching -->
    <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by subject...">
    <button type="submit">Search</button>
    <?php if ($search !== ''): ?><a href="agent-dashboard.php">Clear</a><?php endif; ?>
</form>

<!-- Switch between the open-work view and the full history view -->
<p class="view-tabs">
    <?php if ($view === 'all'): ?>
        <a href="agent-dashboard.php">Open</a> ·
        <strong>History</strong>
    <?php else: ?>
        <strong>Open</strong> ·
        <a href="agent-dashboard.php?view=all">History</a>
    <?php endif; ?>
    — <?= $view === 'all' ? 'all tickets' : 'open tickets only' ?>
</p>

<h2><?= $view === 'all' ? 'All Tickets' : 'Open Tickets' ?> (<?= count($tickets) ?>)</h2>

<?php if (count($tickets) === 0): ?>
    <p>No <?= $view === 'all' ? '' : 'open ' ?>tickets<?= $search !== '' ? ' match your search' : '' ?>.</p>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>User</th>
            <?php if ($view === 'all'): ?><th>Status</th><?php endif; ?>
            <th>Submitted</th>
            <th></th>
        </tr>
        <?php foreach ($tickets as $t): ?>
            <tr>
                <td>#<?= $t['ticket_id'] ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= htmlspecialchars($t['user_full_name']) ?></td>
                <?php if ($view === 'all'): ?><td><?= status_badge($t['status']) ?></td><?php endif; ?>
                <td><?= $t['submitted_at'] ?></td>
                <td><a href="agent-ticket.php?id=<?= $t['ticket_id'] ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php require 'footer.php'; ?>