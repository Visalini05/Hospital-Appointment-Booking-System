<?php
require __DIR__ . '/database/connect.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM doctors WHERE doctor_id = ?');
$stmt->execute([$id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    header('Location: doctors.php');
    exit;
}

$pageTitle = $doc['name'];
$rating = doctorRating($pdo, $id);

$rstmt = $pdo->prepare("SELECT * FROM reviews WHERE doctor_id = ? AND status = 'Approved' ORDER BY created_at DESC LIMIT 20");
$rstmt->execute([$id]);
$reviews = $rstmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<p class="meta"><a href="doctors.php">&larr; Back to Doctors</a></p>

<div class="doctor-profile">
    <div class="doc-avatar doc-avatar-lg" style="background:<?php echo doctorAvatarColor($id); ?>"><?php echo doctorAvatarInitials($doc['name']); ?></div>
    <div>
        <h1 style="margin-bottom:4px;"><?php echo htmlspecialchars($doc['name']); ?></h1>
        <div class="dept"><?php echo htmlspecialchars($doc['department']); ?></div>
        <div class="doc-rating" style="margin-top:8px;">
            <span class="stars"><?php echo ratingStars($rating['avg']); ?></span>
            <?php if ($rating['count'] > 0): ?>
                <span class="meta"><?php echo $rating['avg']; ?> out of 5 (<?php echo $rating['count']; ?> review<?php echo $rating['count'] === 1 ? '' : 's'; ?>)</span>
            <?php else: ?>
                <span class="meta">No reviews yet</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns:2fr 1fr;margin-top:20px;">
    <div class="card">
        <h3>About</h3>
        <p><?php echo nl2br(htmlspecialchars($doc['bio'])); ?></p>
        <h3 style="margin-top:16px;">Details</h3>
        <p class="meta">Qualification: <?php echo htmlspecialchars($doc['qualification']); ?></p>
        <p class="meta">Experience: <?php echo (int)$doc['experience']; ?> years</p>
        <p class="meta">Available Days: <?php echo htmlspecialchars($doc['available_days']); ?></p>
        <p class="meta">Available Time: <?php echo htmlspecialchars($doc['available_time']); ?></p>
        <p class="meta">Consultation Fee: ₹<?php echo (int)$doc['fee']; ?></p>
    </div>
    <div class="card">
        <h3>Book with <?php echo htmlspecialchars(explode(' ', $doc['name'])[1] ?? $doc['name']); ?></h3>
        <p class="meta">Pick a slot that works for you — availability updates live.</p>
        <a class="btn btn-primary" href="appointment.php?doctor_id=<?php echo $id; ?>" style="width:100%;text-align:center;">Book Appointment</a>
    </div>
</div>

<h2 class="section-title" style="margin-top:28px;">Patient Reviews</h2>
<?php if (empty($reviews)): ?>
    <p class="meta">No reviews yet for this doctor.</p>
<?php else: ?>
<div class="review-list">
    <?php foreach ($reviews as $r): ?>
    <div class="review-card">
        <div class="review-head">
            <strong><?php echo htmlspecialchars($r['patient_name']); ?></strong>
            <span class="stars"><?php echo ratingStars((float)$r['rating']); ?></span>
        </div>
        <?php if (!empty($r['comment'])): ?><p><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p><?php endif; ?>
        <p class="meta"><?php echo htmlspecialchars(date('d M Y', strtotime($r['created_at']))); ?></p>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
