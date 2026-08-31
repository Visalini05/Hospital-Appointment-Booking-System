<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Manage Doctors';
$inSubfolder = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM doctors WHERE doctor_id = ?');
    $stmt->execute([$delId]);
    header('Location: doctors.php?msg=deleted');
    exit;
}

$doctors = $pdo->query('SELECT * FROM doctors ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
require __DIR__ . '/../includes/header.php';
?>
<h1>Manage Doctors</h1>
<p><a href="dashboard.php">&larr; Back to Dashboard</a> | <a href="add_doctor.php" class="btn btn-green" style="margin-left:10px;">+ Add Doctor</a></p>
<?php if (($_GET['msg'] ?? '') === 'deleted'): ?><div class="alert alert-success">Doctor removed.</div><?php endif; ?>
<?php if (($_GET['msg'] ?? '') === 'added'): ?><div class="alert alert-success">Doctor added successfully.</div><?php endif; ?>
<?php if (($_GET['msg'] ?? '') === 'updated'): ?><div class="alert alert-success">Doctor details updated.</div><?php endif; ?>

<div class="table-wrap">
<table>
    <tr><th>Name</th><th>Department</th><th>Experience</th><th>Fee</th><th>Phone</th><th>Available</th><th>Action</th></tr>
    <?php foreach ($doctors as $d):
        $rating = doctorRating($pdo, (int)$d['doctor_id']);
    ?>
    <tr>
        <td><?php echo htmlspecialchars($d['name']); ?><br><span class="meta"><?php echo $rating['count'] > 0 ? $rating['avg'] . ' ★ (' . $rating['count'] . ')' : 'No reviews'; ?></span></td>
        <td><?php echo htmlspecialchars($d['department']); ?></td>
        <td><?php echo (int)$d['experience']; ?> yrs</td>
        <td>₹<?php echo (int)$d['fee']; ?></td>
        <td><?php echo htmlspecialchars($d['phone']); ?></td>
        <td><?php echo htmlspecialchars($d['available_days']); ?>, <?php echo htmlspecialchars($d['available_time']); ?></td>
        <td>
            <div class="action-group">
                <a href="edit_doctor.php?id=<?php echo (int)$d['doctor_id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="POST" action="doctors.php" onsubmit="return confirm('Remove this doctor?');">
                    <input type="hidden" name="delete_id" value="<?php echo (int)$d['doctor_id']; ?>">
                    <button type="submit" class="btn btn-red btn-sm">Delete</button>
                </form>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
