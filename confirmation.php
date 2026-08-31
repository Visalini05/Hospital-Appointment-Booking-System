<?php
require __DIR__ . '/database/connect.php';
$pageTitle = 'Booking Confirmation';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT a.*, p.name AS patient_name, d.name AS doctor_name, d.department
    FROM appointments a
    JOIN patients p ON p.patient_id = a.patient_id
    JOIN doctors d ON d.doctor_id = a.doctor_id
    WHERE a.appointment_id = ?');
$stmt->execute([$id]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<h1>Booking Confirmation</h1>
<?php if (!$appt): ?>
    <div class="alert alert-error">Appointment not found.</div>
    <a class="btn btn-primary" href="appointment.php">Book an Appointment</a>
<?php else: ?>
    <div class="alert alert-success">Your appointment has been booked successfully!</div>
    <div class="confirm-box" id="printArea">
        <table>
            <tr><td><strong>Appointment ID</strong></td><td><?php echo formatApptId((int)$appt['appointment_id']); ?></td></tr>
            <tr><td><strong>Patient Name</strong></td><td><?php echo htmlspecialchars($appt['patient_name']); ?></td></tr>
            <tr><td><strong>Doctor</strong></td><td><?php echo htmlspecialchars($appt['doctor_name']); ?> (<?php echo htmlspecialchars($appt['department']); ?>)</td></tr>
            <tr><td><strong>Date</strong></td><td><?php echo htmlspecialchars($appt['appointment_date']); ?></td></tr>
            <tr><td><strong>Time</strong></td><td><?php echo htmlspecialchars($appt['appointment_time']); ?></td></tr>
            <tr><td><strong>Status</strong></td><td><span class="status status-<?php echo htmlspecialchars($appt['status']); ?>"><?php echo htmlspecialchars($appt['status']); ?></span></td></tr>
        </table>
    </div>
    <p style="margin-top:16px;" class="no-print">
        <a class="btn btn-primary" href="patient/dashboard.php">View My Appointments</a>
        <a class="btn btn-outline" href="index.php">Back to Home</a>
        <button type="button" class="btn btn-outline" onclick="window.print()">🖨 Print</button>
    </p>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
