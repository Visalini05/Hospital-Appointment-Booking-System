<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Manage Appointments';
$inSubfolder = true;

// Handle status update / delete.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $id = (int)$_POST['appointment_id'];
        $status = $_POST['status'];
        if (in_array($status, ['Confirmed', 'Pending', 'Cancelled'], true)) {
            $stmt = $pdo->prepare('UPDATE appointments SET status = ? WHERE appointment_id = ?');
            $stmt->execute([$status, $id]);
        }
    } elseif (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare('DELETE FROM appointments WHERE appointment_id = ?');
        $stmt->execute([(int)$_POST['delete_id']]);
    }
    header('Location: appointments.php');
    exit;
}

$search = trim($_GET['search'] ?? '');
$sql = 'SELECT a.*, d.name AS doctor_name, p.name AS patient_name, p.phone AS patient_phone
        FROM appointments a
        JOIN doctors d ON d.doctor_id = a.doctor_id
        JOIN patients p ON p.patient_id = a.patient_id';
$params = [];
if ($search !== '') {
    $sql .= ' WHERE p.name LIKE ?';
    $params[] = '%' . $search . '%';
}
$sql .= ' ORDER BY a.appointment_id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../includes/header.php';
?>
<h1>Manage Appointments</h1>
<p><a href="dashboard.php">&larr; Back to Dashboard</a> | <a href="export.php" class="btn btn-outline" style="margin-left:10px;">⬇ Export CSV</a></p>

<form method="GET" action="appointments.php" style="max-width:400px;margin-bottom:16px;">
    <div class="form-row">
        <label>Search by Patient Name</label>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="appointments.php" class="btn btn-outline">Clear</a>
</form>

<table>
    <tr><th>ID</th><th>Patient</th><th>Phone</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
    <?php foreach ($appointments as $a): ?>
    <tr>
        <td><?php echo formatApptId((int)$a['appointment_id']); ?></td>
        <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
        <td><?php echo htmlspecialchars($a['patient_phone']); ?></td>
        <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
        <td><span class="status status-<?php echo htmlspecialchars($a['status']); ?>"><?php echo htmlspecialchars($a['status']); ?></span></td>
        <td>
            <div class="action-group">
                <form method="POST" action="appointments.php" class="status-form">
                    <input type="hidden" name="appointment_id" value="<?php echo (int)$a['appointment_id']; ?>">
                    <select name="status">
                        <?php foreach (['Confirmed', 'Pending', 'Cancelled'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $s === $a['status'] ? 'selected' : ''; ?>><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status" value="1" class="btn btn-green btn-sm">Save</button>
                </form>
                <form method="POST" action="appointments.php" onsubmit="return confirm('Delete this appointment permanently?');">
                    <input type="hidden" name="delete_id" value="<?php echo (int)$a['appointment_id']; ?>">
                    <button type="submit" class="btn btn-red btn-sm">Delete</button>
                </form>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
