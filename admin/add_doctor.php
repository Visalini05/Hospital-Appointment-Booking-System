<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Add Doctor';
$inSubfolder = true;
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
        $stmt = $pdo->prepare('INSERT INTO doctors (name, department, qualification, experience, phone, email, available_days, available_time, bio, fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $department, $qualification, (int)$experience, $phone, $email, $days, $time, $bio, $fee !== '' ? (int)$fee : 500]);
        header('Location: doctors.php?msg=added');
        exit;
    }
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Add Doctor</h1>
<p><a href="doctors.php">&larr; Back to Doctors</a></p>
<?php if (!empty($errors)): ?>
<div class="alert alert-error"><ul style="margin:0 0 0 18px;"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form method="POST" action="add_doctor.php">
    <div class="form-row"><label>Doctor Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required></div>
    <div class="form-row">
        <label>Department</label>
        <select name="department" required>
            <option value="">-- Select Department --</option>
            <?php foreach (DEPARTMENTS as $d): ?>
            <option value="<?php echo htmlspecialchars($d); ?>" <?php echo (($_POST['department'] ?? '') === $d) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-row"><label>Qualification</label><input type="text" name="qualification" value="<?php echo htmlspecialchars($_POST['qualification'] ?? ''); ?>"></div>
    <div class="form-row"><label>Experience (years)</label><input type="number" name="experience" min="0" value="<?php echo htmlspecialchars($_POST['experience'] ?? ''); ?>" required></div>
    <div class="form-row"><label>Phone</label><input type="tel" name="phone" maxlength="10" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"></div>
    <div class="form-row"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"></div>
    <div class="form-row"><label>Available Days</label><input type="text" name="available_days" placeholder="e.g. Mon-Sat" value="<?php echo htmlspecialchars($_POST['available_days'] ?? ''); ?>"></div>
    <div class="form-row"><label>Available Time</label><input type="text" name="available_time" placeholder="e.g. 09:00 AM - 05:00 PM" value="<?php echo htmlspecialchars($_POST['available_time'] ?? ''); ?>"></div>
    <div class="form-row"><label>Consultation Fee (₹)</label><input type="number" name="fee" min="0" placeholder="e.g. 500" value="<?php echo htmlspecialchars($_POST['fee'] ?? ''); ?>"></div>
    <div class="form-row"><label>Bio</label><textarea name="bio" rows="4" placeholder="Short professional bio shown on the doctor's public profile"><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea></div>
    <button type="submit" class="btn btn-primary">Add Doctor</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
