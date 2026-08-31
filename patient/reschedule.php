<?php
session_start();
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Reschedule Appointment';
$inSubfolder = true;

$appointmentId = (int)($_GET['appointment_id'] ?? $_POST['appointment_id'] ?? 0);
$phone = trim($_GET['phone'] ?? $_POST['phone'] ?? '');

// Ownership check: the appointment must belong to a patient with this phone number.
$stmt = $pdo->prepare('SELECT a.*, d.name AS doctor_name, d.department
    FROM appointments a
    JOIN doctors d ON d.doctor_id = a.doctor_id
    JOIN patients p ON p.patient_id = a.patient_id
    WHERE a.appointment_id = ? AND p.phone = ?');
$stmt->execute([$appointmentId, $phone]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appt || $appt['status'] === 'Cancelled') {
    header('Location: dashboard.php?phone=' . urlencode($phone));
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newDate = trim($_POST['appointment_date'] ?? '');
    $newTime = trim($_POST['appointment_time'] ?? '');
    $today = date('Y-m-d');

    if ($newDate === '' || $newDate < $today) {
        $errors[] = 'Please choose a valid date that is not in the past.';
    }
    if (!in_array($newTime, TIME_SLOTS, true)) {
        $errors[] = 'Please select a valid time slot.';
    }
    if (empty($errors)) {
        $dupe = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != "Cancelled" AND appointment_id != ?');
        $dupe->execute([$appt['doctor_id'], $newDate, $newTime, $appointmentId]);
        if ((int)$dupe->fetchColumn() > 0) {
            $errors[] = 'This doctor is already booked at that date and time. Please pick another slot.';
        }
    }
    if (empty($errors)) {
        $upd = $pdo->prepare('UPDATE appointments SET appointment_date = ?, appointment_time = ?, status = "Confirmed" WHERE appointment_id = ?');
        $upd->execute([$newDate, $newTime, $appointmentId]);
        header('Location: dashboard.php?phone=' . urlencode($phone) . '&msg=rescheduled');
        exit;
    }
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Reschedule Appointment</h1>
<p class="meta">Appointment <?php echo formatApptId((int)$appt['appointment_id']); ?> with <?php echo htmlspecialchars($appt['doctor_name']); ?> (<?php echo htmlspecialchars($appt['department']); ?>)</p>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul style="margin:0 0 0 18px;"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST" action="reschedule.php?appointment_id=<?php echo (int)$appointmentId; ?>&phone=<?php echo urlencode($phone); ?>" style="max-width:420px;">
    <input type="hidden" name="appointment_id" value="<?php echo (int)$appointmentId; ?>">
    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
    <div class="form-row">
        <label>Current Slot</label>
        <p class="meta"><?php echo htmlspecialchars($appt['appointment_date']); ?> at <?php echo htmlspecialchars($appt['appointment_time']); ?></p>
    </div>
    <div class="form-row">
        <label for="appointment_date">New Date</label>
        <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($appt['appointment_date']); ?>" required>
    </div>
    <div class="form-row">
        <label for="appointment_time">New Time Slot</label>
        <select id="appointment_time" name="appointment_time" required>
            <?php foreach (TIME_SLOTS as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $s === $appt['appointment_time'] ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Confirm New Slot</button>
    <a href="dashboard.php?phone=<?php echo urlencode($phone); ?>" class="btn btn-outline">Cancel</a>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
