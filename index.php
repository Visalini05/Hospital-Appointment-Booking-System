<?php
require __DIR__ . '/database/connect.php';
$pageTitle = 'Home';
$doctorCount = (int)$pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn();
$patientCount = (int)$pdo->query('SELECT COUNT(DISTINCT phone) FROM patients')->fetchColumn();
$apptCount = (int)$pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$deptCount = count(DEPARTMENTS);
$featured = $pdo->query('SELECT * FROM doctors ORDER BY RANDOM() LIMIT 3')->fetchAll(PDO::FETCH_ASSOC);

$testimonials = $pdo->query("SELECT r.*, d.name AS doctor_name FROM reviews r
    JOIN doctors d ON d.doctor_id = r.doctor_id
    WHERE r.status = 'Approved' AND r.rating >= 4 AND r.comment != ''
    ORDER BY r.created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <h1>Your Health, Our Priority</h1>
    <p>Book appointments online with <?php echo $doctorCount; ?> experienced doctors across <?php echo $deptCount; ?> departments.</p>
    <a href="appointment.php" class="btn btn-primary">Book Appointment</a>
    <a href="services.php" class="btn btn-outline">Explore Services</a>
</section>

<div class="stat-cards">
    <div class="stat"><div class="stat-icon">👨‍⚕️</div><div class="num"><?php echo $doctorCount; ?></div>Specialist Doctors</div>
    <div class="stat"><div class="stat-icon">🏬</div><div class="num"><?php echo $deptCount; ?></div>Departments</div>
    <div class="stat"><div class="stat-icon">📅</div><div class="num"><?php echo $apptCount; ?></div>Appointments Booked</div>
    <div class="stat"><div class="stat-icon">🙂</div><div class="num"><?php echo $patientCount; ?></div>Patients Served</div>
</div>

<h2>Featured Doctors</h2>
<div class="grid">
<?php foreach ($featured as $doc): $rating = doctorRating($pdo, (int)$doc['doctor_id']); ?>
    <div class="card doctor-card">
        <div class="doc-avatar" style="background:<?php echo doctorAvatarColor((int)$doc['doctor_id']); ?>"><?php echo doctorAvatarInitials($doc['name']); ?></div>
        <h3><a href="doctor.php?id=<?php echo (int)$doc['doctor_id']; ?>"><?php echo htmlspecialchars($doc['name']); ?></a></h3>
        <div class="dept"><?php echo htmlspecialchars($doc['department']); ?></div>
        <div class="meta"><?php echo (int)$doc['experience']; ?> years experience</div>
        <div class="doc-rating"><span class="stars"><?php echo ratingStars($rating['avg']); ?></span></div>
        <a class="btn btn-green" href="appointment.php?doctor_id=<?php echo (int)$doc['doctor_id']; ?>">Book Appointment</a>
    </div>
<?php endforeach; ?>
</div>

<h2 style="margin-top:32px;">Our Services</h2>
<div class="grid">
    <div class="card"><h3>🩺 Specialist Care</h3><p class="meta">Consult experienced specialists across six departments.</p></div>
    <div class="card"><h3>🚑 Emergency</h3><p class="meta">24x7 emergency support available.</p></div>
    <div class="card"><h3>🧪 Lab Test</h3><p class="meta">On-site diagnostic and lab facilities.</p></div>
</div>
<p style="margin-top:14px;"><a href="services.php" class="btn btn-outline">See All Services</a></p>

<?php if (!empty($testimonials)): ?>
<h2 style="margin-top:32px;">What Our Patients Say</h2>
<div class="grid">
    <?php foreach ($testimonials as $t): ?>
    <div class="card review-card">
        <span class="stars"><?php echo ratingStars((float)$t['rating']); ?></span>
        <p>"<?php echo nl2br(htmlspecialchars($t['comment'])); ?>"</p>
        <p class="meta">&mdash; <?php echo htmlspecialchars($t['patient_name']); ?>, on <?php echo htmlspecialchars($t['doctor_name']); ?></p>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
