<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Admin Dashboard';
$inSubfolder = true;

$totalDoctors = (int)$pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn();
$totalAppointments = (int)$pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE appointment_date = ?');
$stmt->execute([date('Y-m-d')]);
$todayAppointments = (int)$stmt->fetchColumn();
$cancelled = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Cancelled'")->fetchColumn();
$unreadMessages = (int)$pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$pendingReviews = (int)$pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'Pending'")->fetchColumn();

$recent = $pdo->query('SELECT a.*, d.name AS doctor_name, p.name AS patient_name
    FROM appointments a
    JOIN doctors d ON d.doctor_id = a.doctor_id
    JOIN patients p ON p.patient_id = a.patient_id
    ORDER BY a.appointment_id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../includes/header.php';
?>
<section class="admin-hero">
    <div>
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?> 👋</h1>
        <p>Here's what's happening at ABC Hospital today.</p>
    </div>
    <a href="logout.php" class="btn btn-outline">Logout</a>
</section>

<div class="quick-actions">
    <a href="doctors.php" class="quick-card">
        <span class="quick-icon">🩺</span>
        <span class="quick-label">Manage Doctors</span>
    </a>
    <a href="add_doctor.php" class="quick-card">
        <span class="quick-icon">➕</span>
        <span class="quick-label">Add Doctor</span>
    </a>
    <a href="appointments.php" class="quick-card">
        <span class="quick-icon">📅</span>
        <span class="quick-label">Manage Appointments</span>
    </a>
    <a href="messages.php" class="quick-card">
        <span class="quick-icon">✉️</span>
        <span class="quick-label">Messages<?php if ($unreadMessages > 0): ?> <span class="badge"><?php echo $unreadMessages; ?></span><?php endif; ?></span>
    </a>
    <a href="reviews.php" class="quick-card">
        <span class="quick-icon">⭐</span>
        <span class="quick-label">Reviews<?php if ($pendingReviews > 0): ?> <span class="badge"><?php echo $pendingReviews; ?></span><?php endif; ?></span>
    </a>
    <a href="reports.php" class="quick-card">
        <span class="quick-icon">📊</span>
        <span class="quick-label">Reports &amp; Export</span>
    </a>
</div>

<div class="stat-cards">
    <div class="stat"><div class="stat-icon"></div><div class="num"><?php echo $totalDoctors; ?></div>Total Doctors</div>
    <div class="stat"><div class="stat-icon"></div><div class="num"><?php echo $totalAppointments; ?></div>Total Appointments</div>
    <div class="stat"><div class="stat-icon"></div><div class="num"><?php echo $todayAppointments; ?></div>Today's Appointments</div>
    <div class="stat"><div class="stat-icon"></div><div class="num"><?php echo $cancelled; ?></div>Cancelled</div>
</div>

<h2 class="section-title">Recent Appointments</h2>
<div class="table-wrap">
<table>
    <tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
    <?php foreach ($recent as $a): ?>
    <tr>
        <td><?php echo formatApptId((int)$a['appointment_id']); ?></td>
        <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
        <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
        <td><span class="status status-<?php echo htmlspecialchars($a['status']); ?>"><?php echo htmlspecialchars($a['status']); ?></span></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($recent)): ?>
    <tr><td colspan="6" style="text-align:center;color:var(--muted);">No appointments yet.</td></tr>
    <?php endif; ?>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
