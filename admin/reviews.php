<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Manage Reviews';
$inSubfolder = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'Approved' WHERE review_id = ?");
        $stmt->execute([(int)$_POST['approve_id']]);
    } elseif (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare('DELETE FROM reviews WHERE review_id = ?');
        $stmt->execute([(int)$_POST['delete_id']]);
    }
    header('Location: reviews.php');
    exit;
}

$reviews = $pdo->query("SELECT r.*, d.name AS doctor_name FROM reviews r
    JOIN doctors d ON d.doctor_id = r.doctor_id
    ORDER BY (r.status = 'Pending') DESC, r.review_id DESC")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../includes/header.php';
?>
<h1>Manage Reviews</h1>
<p><a href="dashboard.php">&larr; Back to Dashboard</a></p>

<?php if (empty($reviews)): ?>
    <p class="meta" style="margin-top:16px;">No reviews submitted yet.</p>
<?php else: ?>
<div class="message-list">
    <?php foreach ($reviews as $r): ?>
    <div class="card message-card <?php echo $r['status'] === 'Pending' ? 'message-unread' : ''; ?>">
        <div class="message-head">
            <div>
                <strong><?php echo htmlspecialchars($r['patient_name']); ?></strong>
                <span class="meta">on <?php echo htmlspecialchars($r['doctor_name']); ?></span>
                <span class="status status-<?php echo $r['status'] === 'Pending' ? 'Pending' : 'Confirmed'; ?>"><?php echo htmlspecialchars($r['status']); ?></span>
            </div>
            <span class="stars"><?php echo ratingStars((float)$r['rating']); ?></span>
        </div>
        <?php if (!empty($r['comment'])): ?><p><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p><?php endif; ?>
        <p class="meta"><?php echo htmlspecialchars(date('d M Y', strtotime($r['created_at']))); ?></p>
        <div class="action-group" style="flex-direction:row;">
            <?php if ($r['status'] === 'Pending'): ?>
            <form method="POST" action="reviews.php">
                <input type="hidden" name="approve_id" value="<?php echo (int)$r['review_id']; ?>">
                <button type="submit" class="btn btn-green btn-sm">Approve</button>
            </form>
            <?php endif; ?>
            <form method="POST" action="reviews.php" onsubmit="return confirm('Delete this review?');">
                <input type="hidden" name="delete_id" value="<?php echo (int)$r['review_id']; ?>">
                <button type="submit" class="btn btn-red btn-sm">Delete</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
