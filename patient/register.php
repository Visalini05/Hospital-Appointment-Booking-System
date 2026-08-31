<?php
session_start();
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Create Account';
$inSubfolder = true;

if (!empty($_SESSION['patient_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $old = $_POST;

    if ($name === '' || !preg_match('/^[A-Za-z ]{2,50}$/', $name)) {
        $errors[] = 'Please enter a valid name (letters only, 2-50 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Phone number must be exactly 10 digits.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE email = ? AND password IS NOT NULL");
        $check->execute([$email]);
        if ((int)$check->fetchColumn() > 0) {
            $errors[] = 'An account with this email already exists. Please login instead.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO patients (name, phone, email, password, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $phone, $email, password_hash($password, PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
        $patientId = (int)$pdo->lastInsertId();

        $_SESSION['patient_id'] = $patientId;
        $_SESSION['patient_name'] = $name;
        $_SESSION['patient_phone'] = $phone;

        header('Location: dashboard.php?welcome=1');
        exit;
    }
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Create Your Account</h1>
<p class="meta" style="margin-top:-6px;margin-bottom:18px;">Save your details once, then book and track appointments without retyping them every time.</p>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <strong>Please fix the following:</strong>
    <ul style="margin:6px 0 0 18px;">
        <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="register.php" style="max-width:460px;">
    <div class="form-row">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" required>
    </div>
    <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
    </div>
    <div class="form-row">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" maxlength="10" value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>" required>
    </div>
    <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="form-row">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit" class="btn btn-primary">Create Account</button>
</form>
<p class="meta" style="margin-top:14px;">Already have an account? <a href="login.php">Login here</a>.</p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
