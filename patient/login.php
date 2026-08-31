<?php
session_start();
require __DIR__ . '/../database/connect.php';
$pageTitle = 'Patient Login';
$inSubfolder = true;

if (!empty($_SESSION['patient_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM patients WHERE email = ? AND password IS NOT NULL ORDER BY patient_id DESC LIMIT 1");
    $stmt->execute([$email]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient && password_verify($password, $patient['password'])) {
        $_SESSION['patient_id'] = (int)$patient['patient_id'];
        $_SESSION['patient_name'] = $patient['name'];
        $_SESSION['patient_phone'] = $patient['phone'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid email or password.';
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Patient Login</h1>
<?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form method="POST" action="login.php" style="max-width:380px;">
    <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<p class="meta" style="margin-top:14px;">Don't have an account? <a href="register.php">Create one</a>.</p>
<p class="meta">Booked as a guest? You can still <a href="dashboard.php">look up appointments by phone number</a>.</p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
