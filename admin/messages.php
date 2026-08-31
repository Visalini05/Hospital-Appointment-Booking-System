<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Messages';
$inSubfolder = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_read'])) {
        $stmt = $pdo->prepare('UPDATE messages SET is_read = 1 WHERE message_id = ?');
        $stmt->execute([(int)$_POST['message_id']]);
    } elseif (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare('DELETE FROM messages WHERE message_id = ?');
        $stmt->execute([(int)$_POST['delete_id']]);
    }
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query('SELECT * FROM messages ORDER BY is_read ASC, message_id DESC')->fetchAll(PDO::FETCH_ASSOC);
require __DIR__ . '/../includes/header.php';
?>
<h1>Contact Messages</h1>
<p><a href="dashboard.php">&larr; Back to Dashboard</a></p>

<?php if (empty($messages)): ?>
    <p class="meta" style="margin-top:16px;">No messages yet.</p>
<?php else: ?>
<div class="message-list">
    <?php foreach ($messages as $m): ?>
    <div class="card message-card <?php echo $m['is_read'] ? '' : 'message-unread'; ?>">
        <div class="message-head">
            <div>
                <strong><?php echo htmlspecialchars($m['name']); ?></strong>
                <span class="meta">&lt;<?php echo htmlspecialchars($m['email']); ?>&gt;</span>
                <?php if (!$m['is_read']): ?><span class="status status-Pending">New</span><?php endif; ?>
            </div>
            <span class="meta"><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($m['created_at']))); ?></span>
        </div>
        <?php if (!empty($m['subject'])): ?><p><strong>Subject:</strong> <?php echo htmlspecialchars($m['subject']); ?></p><?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars($m['message'])); ?></p>
        <div class="action-group" style="flex-direction:row;">
            <?php if (!$m['is_read']): ?>
            <form method="POST" action="messages.php">
                <input type="hidden" name="message_id" value="<?php echo (int)$m['message_id']; ?>">
                <button type="submit" name="mark_read" value="1" class="btn btn-outline btn-sm">Mark Read</button>
            </form>
            <?php endif; ?>
            <form method="POST" action="messages.php" onsubmit="return confirm('Delete this message?');">
                <input type="hidden" name="delete_id" value="<?php echo (int)$m['message_id']; ?>">
                <button type="submit" class="btn btn-red btn-sm">Delete</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
