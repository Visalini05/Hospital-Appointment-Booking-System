<?php
session_start();
require __DIR__ . '/../database/connect.php';
$pageTitle = 'My Appointments';
$inSubfolder = true;

$loggedIn = !empty($_SESSION['patient_id']);
$phone = $loggedIn ? trim($_SESSION['patient_phone'] ?? '') : trim($_GET['phone'] ?? ($_SESSION['lookup_phone'] ?? ''));
$appointments = [];
$searched = false;

if ($phone !== '') {
    $searched = true;
    if (!$loggedIn) { $_SESSION['lookup_phone'] = $phone; }
    $stmt = $pdo->prepare('SELECT a.*, d.name AS doctor_name, d.department, d.doctor_id, p.name AS patient_name
        FROM appointments a
        JOIN doctors d ON d.doctor_id = a.doctor_id
        JOIN patients p ON p.patient_id = a.patient_id
        WHERE p.phone = ?
        ORDER BY a.appointment_date DESC, a.appointment_id DESC');
    $stmt->execute([$phone]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Upcoming-appointment reminder: next Confirmed appointment within the next 3 days.
$reminder = null;
$today = date('Y-m-d');
$soon = date('Y-m-d', strtotime('+3 days'));
foreach ($appointments as $a) {
    if ($a['status'] === 'Confirmed' && $a['appointment_date'] >= $today && $a['appointment_date'] <= $soon) {
        $reminder = $a;
        break;
    }
}

// Which appointments already have a review?
$reviewed = [];
if (!empty($appointments)) {
    $ids = array_column($appointments, 'appointment_id');
    $in = implode(',', array_fill(0, count($ids), '?'));
    $rstmt = $pdo->prepare("SELECT appointment_id FROM reviews WHERE appointment_id IN ($in)");
    $rstmt->execute($ids);
    $reviewed = array_column($rstmt->fetchAll(PDO::FETCH_ASSOC), 'appointment_id');
}

$flashMsg = '';
if (isset($_GET['msg'])) {
    $map = [
        'cancelled' => 'Appointment cancelled successfully.',
        'rescheduled' => 'Appointment rescheduled successfully.',
        'reviewed' => 'Thanks! Your review has been submitted for approval.',
    ];
    $flashMsg = $map[$_GET['msg']] ?? '';
}
if (isset($_GET['welcome'])) {
    $flashMsg = 'Account created! You are now logged in.';
}

require __DIR__ . '/../includes/header.php';
?>
<h1>My Appointments</h1>

<?php if (!empty($flashMsg)): ?><div class="alert alert-success"><?php echo htmlspecialchars($flashMsg); ?></div><?php endif; ?>

<?php if ($reminder): ?>
<div class="alert alert-success" style="border-color:#93C5FD;background:#EFF6FF;color:#1E3A8A;">
    ⏰ <strong>Upcoming visit:</strong> You have an appointment with <?php echo htmlspecialchars($reminder['doctor_name']); ?>
    on <?php echo htmlspecialchars($reminder['appointment_date']); ?> at <?php echo htmlspecialchars($reminder['appointment_time']); ?>.
</div>
<?php endif; ?>

<?php if ($loggedIn): ?>
    <p class="meta">Showing appointments for <strong><?php echo htmlspecialchars($_SESSION['patient_name']); ?></strong> (<?php echo htmlspecialchars($phone); ?>).</p>
<?php else: ?>
    <form method="GET" action="dashboard.php" style="max-width:400px;margin-bottom:24px;">
        <div class="form-row">
            <label for="phone">Enter your phone number to view your appointments</label>
            <input type="tel" id="phone" name="phone" maxlength="10" value="<?php echo htmlspecialchars($phone); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <p class="meta" style="margin-top:-14px;margin-bottom:20px;">Have an account? <a href="login.php">Login</a> to skip this step next time.</p>
<?php endif; ?>

<?php if ($searched): ?>
    <?php if (empty($appointments)): ?>
        <p>No appointments found for this phone number.</p>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <tr><th>Appointment ID</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?php echo formatApptId((int)$a['appointment_id']); ?></td>
            <td><?php echo htmlspecialchars($a['doctor_name']); ?> (<?php echo htmlspecialchars($a['department']); ?>)</td>
            <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
            <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
            <td><span class="status status-<?php echo htmlspecialchars($a['status']); ?>"><?php echo htmlspecialchars($a['status']); ?></span></td>
            <td>
                <div class="action-group">
                <?php if ($a['status'] !== 'Cancelled'): ?>
                    <a href="reschedule.php?appointment_id=<?php echo (int)$a['appointment_id']; ?>&phone=<?php echo urlencode($phone); ?>" class="btn btn-outline btn-sm">Reschedule</a>
                    <form method="POST" action="cancel.php" onsubmit="return confirm('Cancel this appointment?');" class="action-cell">
                        <input type="hidden" name="appointment_id" value="<?php echo (int)$a['appointment_id']; ?>">
                        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                        <button type="submit" class="btn btn-red btn-sm">Cancel</button>
                    </form>
                <?php endif; ?>
                <?php if ($a['appointment_date'] <= $today && $a['status'] !== 'Cancelled'): ?>
                    <?php if (in_array((int)$a['appointment_id'], $reviewed, true)): ?>
                        <span class="meta">Reviewed ✓</span>
                    <?php else: ?>
                        <a href="review.php?appointment_id=<?php echo (int)$a['appointment_id']; ?>&phone=<?php echo urlencode($phone); ?>" class="btn btn-green btn-sm">Leave a Review</a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($a['status'] === 'Cancelled'): ?><span class="action-dash">&mdash;</span><?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
