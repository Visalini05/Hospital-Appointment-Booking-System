<?php
require __DIR__ . '/database/connect.php';
$pageTitle = 'Doctors';

$search = trim($_GET['search'] ?? '');
$dept = trim($_GET['department'] ?? '');

$sql = 'SELECT * FROM doctors WHERE 1=1';
$params = [];
if ($search !== '') {
    $sql .= ' AND name LIKE ?';
    $params[] = '%' . $search . '%';
}
if ($dept !== '') {
    $sql .= ' AND department = ?';
    $params[] = $dept;
}
$sql .= ' ORDER BY department, name';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<h1>Our Doctors</h1>

<form method="GET" action="doctors.php" class="filter-bar">
    <div class="form-row" style="margin-bottom:0;flex:1;min-width:180px;">
        <input type="text" name="search" placeholder="Search by doctor name..." value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div class="form-row" style="margin-bottom:0;min-width:180px;">
        <select name="department">
            <option value="">All Departments</option>
            <?php foreach (DEPARTMENTS as $d): ?>
            <option value="<?php echo htmlspecialchars($d); ?>" <?php echo $dept === $d ? 'selected' : ''; ?>><?php echo htmlspecialchars($d); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <?php if ($search !== '' || $dept !== ''): ?><a href="doctors.php" class="btn btn-outline">Clear</a><?php endif; ?>
</form>

<?php if (empty($doctors)): ?>
    <p class="meta" style="margin-top:20px;">No doctors match your search. Try a different name or department.</p>
<?php endif; ?>

<div class="grid">
<?php foreach ($doctors as $doc):
    $rating = doctorRating($pdo, (int)$doc['doctor_id']);
?>
    <div class="card doctor-card">
        <div class="doc-avatar" style="background:<?php echo doctorAvatarColor((int)$doc['doctor_id']); ?>"><?php echo doctorAvatarInitials($doc['name']); ?></div>
        <h3><a href="doctor.php?id=<?php echo (int)$doc['doctor_id']; ?>"><?php echo htmlspecialchars($doc['name']); ?></a></h3>
        <div class="dept"><?php echo htmlspecialchars($doc['department']); ?></div>
        <div class="meta"><?php echo htmlspecialchars($doc['qualification']); ?></div>
        <div class="meta"><?php echo (int)$doc['experience']; ?> years experience</div>
        <div class="meta">Available: <?php echo htmlspecialchars($doc['available_days']); ?></div>
        <div class="meta">Time: <?php echo htmlspecialchars($doc['available_time']); ?></div>
        <div class="meta">Fee: ₹<?php echo (int)$doc['fee']; ?></div>
        <div class="doc-rating">
            <span class="stars"><?php echo ratingStars($rating['avg']); ?></span>
            <?php if ($rating['count'] > 0): ?>
                <span class="meta"><?php echo $rating['avg']; ?> (<?php echo $rating['count']; ?> review<?php echo $rating['count'] === 1 ? '' : 's'; ?>)</span>
            <?php else: ?>
                <span class="meta">No reviews yet</span>
            <?php endif; ?>
        </div>
        <a class="btn btn-outline" href="doctor.php?id=<?php echo (int)$doc['doctor_id']; ?>" style="margin-right:6px;">View Profile</a>
        <a class="btn btn-green" href="appointment.php?doctor_id=<?php echo (int)$doc['doctor_id']; ?>">Book Appointment</a>
    </div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
