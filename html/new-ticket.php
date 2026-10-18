<?php
// ============================================================
// new-ticket.php   —   open a new ticket
// ============================================================
// Shows a form for subject + description. On submit it saves
// the ticket as 'open' and sends the user to the ticket page.
// ============================================================

session_start();
require_once 'db.php';

// Must be logged in first.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject     = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($subject === '' || $description === '') {
        $error = 'Subject and description are required.';
    } else {
        // Insert the ticket. status is always 'open' for a new ticket.
        $add = $pdo->prepare(
            'INSERT INTO tickets (user_id, subject, description, status)
             VALUES (?, ?, ?, "open")'
        );
        $add->execute([$_SESSION['user_id'], $subject, $description]);

        // lastInsertId gives us the ID MySQL just created for this ticket.
        $new_id = $pdo->lastInsertId();
        flash('Ticket #' . $new_id . ' opened — it is now waiting for an agent.');
        header('Location: ticket.php?id=' . $new_id);
        exit;
    }
}

$page_title = 'Open a Ticket';
require 'header.php';
?>
<h1>Open a Ticket</h1>

<?php if ($error !== null): ?>
    <p class="flash-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="auth-form form-wide">
<form method="post">
    <p>
        <label for="subject">Subject</label><br>
        <input type="text" id="subject" name="subject" maxlength="150" required>
    </p>
    <p>
        <label for="description">What is the problem?</label><br>
        <textarea id="description" name="description" rows="6" required></textarea>
    </p>
    <p><button type="submit">Submit Ticket</button></p>
</form>
</div>
<?php require 'footer.php'; ?>