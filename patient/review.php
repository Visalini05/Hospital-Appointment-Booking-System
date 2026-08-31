<?php
session_start();
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Leave a Review';
$inSubfolder = true;

$appointmentId = (int)($_GET['appointment_id'] ?? $_POST['appointment_id'] ?? 0);
$phone = trim($_GET['phone'] ?? $_POST['phone'] ?? '');

$stmt = $pdo->prepare('SELECT a.*, d.name AS doctor_name, d.department, p.name AS patient_name, p.patient_id
    FROM appointments a
    JOIN doctors d ON d.doctor_id = a.doctor_id
    JOIN patients p ON p.patient_id = a.patient_id
    WHERE a.appointment_id = ? AND p.phone = ?');
$stmt->execute([$appointmentId, $phone]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appt || $appt['status'] === 'Cancelled' || $appt['appointment_date'] > date('Y-m-d')) {
    header('Location: dashboard.php?phone=' . urlencode($phone));
    exit;
}

$already = $pdo->prepare('SELECT COUNT(*) FROM reviews WHERE appointment_id = ?');
$already->execute([$appointmentId]);
if ((int)$already->fetchColumn() > 0) {
    header('Location: dashboard.php?phone=' . urlencode($phone));
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Please choose a star rating from 1 to 5.';
    }
    if (strlen($comment) > 500) {
        $errors[] = 'Please keep your comment under 500 characters.';
    }

    if (empty($errors)) {
        $ins = $pdo->prepare('INSERT INTO reviews (doctor_id, patient_id, appointment_id, patient_name, rating, comment, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $ins->execute([$appt['doctor_id'], $appt['patient_id'], $appointmentId, $appt['patient_name'], $rating, $comment, 'Pending', date('Y-m-d H:i:s')]);
        header('Location: dashboard.php?phone=' . urlencode($phone) . '&msg=reviewed');
        exit;
    }
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Leave a Review</h1>
<p class="meta">For your visit with <?php echo htmlspecialchars($appt['doctor_name']); ?> (<?php echo htmlspecialchars($appt['department']); ?>) on <?php echo htmlspecialchars($appt['appointment_date']); ?></p>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul style="margin:0 0 0 18px;"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST" action="review.php?appointment_id=<?php echo (int)$appointmentId; ?>&phone=<?php echo urlencode($phone); ?>" style="max-width:460px;">
    <input type="hidden" name="appointment_id" value="<?php echo (int)$appointmentId; ?>">
    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
    <div class="form-row">
        <label>Rating</label>
        <div class="star-picker">
            <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo $i == 5 ? 'checked' : ''; ?>>
            <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">★</label>
            <?php endfor; ?>
        </div>
    </div>
    <div class="form-row">
        <label for="comment">Comment (optional)</label>
        <textarea id="comment" name="comment" rows="4" maxlength="500" placeholder="Share your experience..."></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit Review</button>
    <a href="dashboard.php?phone=<?php echo urlencode($phone); ?>" class="btn btn-outline">Cancel</a>
</form>
<p class="meta" style="margin-top:12px;">Reviews are checked by our team before they appear on the doctor's profile.</p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
