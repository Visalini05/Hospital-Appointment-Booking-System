<?php
require __DIR__ . '/database/connect.php';
$pageTitle = 'Services';

$counts = [];
$rows = $pdo->query('SELECT department, COUNT(*) AS c FROM doctors GROUP BY department')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) { $counts[$r['department']] = (int)$r['c']; }

$icons = [
    'Cardiology' => '❤️', 'Neurology' => '🧠', 'Pediatrics' => '🧸',
    'Orthopedics' => '🦴', 'Dermatology' => '🩹', 'General Medicine' => '🩺',
];
$blurbs = [
    'Cardiology' => 'Heart health checkups, ECG-based diagnosis and long-term cardiac care.',
    'Neurology' => 'Care for the brain, spine and nervous system, including migraine and stroke recovery.',
    'Pediatrics' => 'Child-friendly care from immunizations to everyday childhood illnesses.',
    'Orthopedics' => 'Bone, joint and sports injury treatment with physiotherapy-led recovery.',
    'Dermatology' => 'Skin, hair and nail care, from acne treatment to cosmetic consultations.',
    'General Medicine' => 'Everyday illness, chronic disease management and full-body checkups.',
];

require __DIR__ . '/includes/header.php';
?>
<h1>Our Services</h1>
<p class="meta" style="margin-top:-6px;margin-bottom:22px;">Explore our departments and book directly with a specialist.</p>

<div class="grid">
<?php foreach (DEPARTMENTS as $dept): ?>
    <div class="card service-card">
        <div class="service-icon"><?php echo $icons[$dept] ?? '🏥'; ?></div>
        <h3><?php echo htmlspecialchars($dept); ?></h3>
        <p class="meta"><?php echo htmlspecialchars($blurbs[$dept] ?? ''); ?></p>
        <p class="meta"><?php echo $counts[$dept] ?? 0; ?> specialist(s) available</p>
        <a class="btn btn-outline" href="doctors.php?department=<?php echo urlencode($dept); ?>">View Doctors</a>
    </div>
<?php endforeach; ?>
    <div class="card service-card">
        <div class="service-icon">🚑</div>
        <h3>24x7 Emergency</h3>
        <p class="meta">Round-the-clock emergency response and critical care support.</p>
        <a class="btn btn-outline" href="contact.php">Contact Us</a>
    </div>
    <div class="card service-card">
        <div class="service-icon">🧪</div>
        <h3>On-site Lab &amp; Diagnostics</h3>
        <p class="meta">Same-day lab testing and diagnostic imaging, no separate appointment needed.</p>
        <a class="btn btn-outline" href="contact.php">Contact Us</a>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
