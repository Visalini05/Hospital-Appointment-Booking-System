<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Reports & Analytics';
$inSubfolder = true;

// Appointments per department.
$byDept = $pdo->query("SELECT d.department, COUNT(*) AS c FROM appointments a
    JOIN doctors d ON d.doctor_id = a.doctor_id
    GROUP BY d.department ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC);
$maxDept = 0;
foreach ($byDept as $r) { $maxDept = max($maxDept, (int)$r['c']); }

// Bookings over the last 14 days (by created_at date).
$days = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $days[$d] = 0;
}
$stmt = $pdo->prepare("SELECT DATE(created_at) AS d, COUNT(*) AS c FROM appointments WHERE DATE(created_at) >= ? GROUP BY DATE(created_at)");
$stmt->execute([date('Y-m-d', strtotime('-13 days'))]);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    if (isset($days[$r['d']])) { $days[$r['d']] = (int)$r['c']; }
}
$maxDay = max(1, max($days));

// Top doctors by bookings.
$topDoctors = $pdo->query("SELECT d.name, d.department, COUNT(*) AS c FROM appointments a
    JOIN doctors d ON d.doctor_id = a.doctor_id
    GROUP BY a.doctor_id ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$maxTop = 0;
foreach ($topDoctors as $r) { $maxTop = max($maxTop, (int)$r['c']); }

// Overall stats.
$total = (int)$pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$cancelled = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Cancelled'")->fetchColumn();
$cancelRate = $total > 0 ? round($cancelled / $total * 100, 1) : 0;
$avgRatingRow = $pdo->query("SELECT AVG(rating) AS a FROM reviews WHERE status = 'Approved'")->fetch(PDO::FETCH_ASSOC);
$avgRating = $avgRatingRow['a'] ? round((float)$avgRatingRow['a'], 1) : 0;

require __DIR__ . '/../includes/header.php';
?>
<h1>Reports &amp; Analytics</h1>
<p><a href="dashboard.php">&larr; Back to Dashboard</a> | <a href="export.php" class="btn btn-outline" style="margin-left:10px;">⬇ Export Appointments CSV</a></p>

<div class="stat-cards">
    <div class="stat"><div class="stat-icon">📅</div><div class="num"><?php echo $total; ?></div>Total Appointments</div>
    <div class="stat"><div class="stat-icon">❌</div><div class="num"><?php echo $cancelRate; ?>%</div>Cancellation Rate</div>
    <div class="stat"><div class="stat-icon">⭐</div><div class="num"><?php echo $avgRating; ?></div>Average Rating</div>
</div>

<div class="grid" style="grid-template-columns:1fr 1fr;align-items:start;">
    <div class="card">
        <h3>Appointments by Department</h3>
        <div class="bar-chart">
            <?php foreach ($byDept as $r): $pct = $maxDept > 0 ? round((int)$r['c'] / $maxDept * 100) : 0; ?>
            <div class="bar-row">
                <div class="bar-label"><?php echo htmlspecialchars($r['department']); ?></div>
                <div class="bar-track"><div class="bar-fill" style="width:<?php echo $pct; ?>%"></div></div>
                <div class="bar-value"><?php echo (int)$r['c']; ?></div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($byDept)): ?><p class="meta">No appointment data yet.</p><?php endif; ?>
        </div>
    </div>

    <div class="card">
        <h3>Top Doctors by Bookings</h3>
        <div class="bar-chart">
            <?php foreach ($topDoctors as $r): $pct = $maxTop > 0 ? round((int)$r['c'] / $maxTop * 100) : 0; ?>
            <div class="bar-row">
                <div class="bar-label"><?php echo htmlspecialchars($r['name']); ?></div>
                <div class="bar-track"><div class="bar-fill bar-fill-green" style="width:<?php echo $pct; ?>%"></div></div>
                <div class="bar-value"><?php echo (int)$r['c']; ?></div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($topDoctors)): ?><p class="meta">No appointment data yet.</p><?php endif; ?>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px;">
    <h3>Bookings — Last 14 Days</h3>
    <div class="trend-chart">
        <?php foreach ($days as $d => $c): $h = $maxDay > 0 ? max(4, round($c / $maxDay * 100)) : 4; ?>
        <div class="trend-bar" title="<?php echo htmlspecialchars($d) . ': ' . $c; ?>">
            <div class="trend-fill" style="height:<?php echo $h; ?>%"></div>
            <span class="trend-label"><?php echo date('d/m', strtotime($d)); ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
