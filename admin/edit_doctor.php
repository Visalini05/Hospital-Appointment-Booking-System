<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Edit Doctor';
$inSubfolder = true;

$id = (int)($_GET['id'] ?? $_POST['doctor_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM doctors WHERE doctor_id = ?');
$stmt->execute([$id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    header('Location: doctors.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $days = trim($_POST['available_days'] ?? '');
    $time = trim($_POST['available_time'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $fee = trim($_POST['fee'] ?? '');

    if ($name === '') $errors[] = 'Doctor name is required.';
    if ($department === '') $errors[] = 'Department is required.';
    if ($experience === '' || !ctype_digit($experience)) $errors[] = 'Experience must be a number.';
    if ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) $errors[] = 'Phone must be 10 digits.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if ($fee !== '' && !ctype_digit($fee)) $errors[] = 'Fee must be a whole number.';

    if (empty($errors)) {
        $upd = $pdo->prepare('UPDATE doctors SET name=?, department=?, qualification=?, experience=?, phone=?, email=?, available_days=?, available_time=?, bio=?, fee=? WHERE doctor_id=?');
        $upd->execute([$name, $department, $qualification, (int)$experience, $phone, $email, $days, $time, $bio, $fee !== '' ? (int)$fee : 0, $id]);
        header('Location: doctors.php?msg=updated');
        exit;
    }
    $doctor = array_merge($doctor, $_POST);
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Edit Doctor</h1>
<p><a href="doctors.php">&larr; Back to Doctors</a></p>
<?php if (!empty($errors)): ?>
<div class="alert alert-error"><ul style="margin:0 0 0 18px;"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form method="POST" action="edit_doctor.php?id=<?php echo $id; ?>">
    <input type="hidden" name="doctor_id" value="<?php echo $id; ?>">
    <div class="form-row"><label>Doctor Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required></div>
    <div class="form-row">
        <label>Department</label>
        <select name="department" required>
            <?php foreach (DEPARTMENTS as $d): ?>
            <option value="<?php echo htmlspecialchars($d); ?>" <?php echo $doctor['department'] === $d ? 'selected' : ''; ?>><?php echo htmlspecialchars($d); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-row"><label>Qualification</label><input type="text" name="qualification" value="<?php echo htmlspecialchars($doctor['qualification']); ?>"></div>
    <div class="form-row"><label>Experience (years)</label><input type="number" name="experience" min="0" value="<?php echo htmlspecialchars($doctor['experience']); ?>" required></div>
    <div class="form-row"><label>Phone</label><input type="tel" name="phone" maxlength="10" value="<?php echo htmlspecialchars($doctor['phone']); ?>"></div>
    <div class="form-row"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>"></div>
    <div class="form-row"><label>Available Days</label><input type="text" name="available_days" value="<?php echo htmlspecialchars($doctor['available_days']); ?>"></div>
    <div class="form-row"><label>Available Time</label><input type="text" name="available_time" value="<?php echo htmlspecialchars($doctor['available_time']); ?>"></div>
    <div class="form-row"><label>Consultation Fee (₹)</label><input type="number" name="fee" min="0" value="<?php echo htmlspecialchars($doctor['fee']); ?>"></div>
    <div class="form-row"><label>Bio</label><textarea name="bio" rows="4"><?php echo htmlspecialchars($doctor['bio']); ?></textarea></div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
