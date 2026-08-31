<?php
require __DIR__ . '/database/connect.php';
$pageTitle = 'Contact Us';

$errors = [];
$success = false;
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $old = $_POST;

    if ($name === '' || strlen($name) < 2) { $errors[] = 'Please enter your name.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please enter a valid email address.'; }
    if ($message === '' || strlen($message) < 5) { $errors[] = 'Please enter a message.'; }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $subject, $message, date('Y-m-d H:i:s')]);
        $success = true;
        $old = [];
    }
}

require __DIR__ . '/includes/header.php';
?>
<h1>Contact Us</h1>

<div class="grid" style="grid-template-columns:1fr 1.3fr;align-items:start;">
    <div class="card">
        <p><strong>Address:</strong> 123 Health Street, Coimbatore, Tamil Nadu</p>
        <p><strong>Phone:</strong> +91 10293 84756</p>
        <p><strong>Email:</strong> info@abchospital.com</p>
        <p><strong>Opening Hours:</strong> Mon - Sat, 8:00 AM - 8:00 PM</p>
        <p><strong>Emergency:</strong> Available 24x7</p>
    </div>

    <div>
        <?php if ($success): ?>
            <div class="alert alert-success">Thanks for reaching out! Our team will get back to you soon.</div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error"><ul style="margin:0 0 0 18px;"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="POST" action="contact.php">
            <div class="form-row">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" required>
            </div>
            <div class="form-row">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
            </div>
            <div class="form-row">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($old['subject'] ?? ''); ?>">
            </div>
            <div class="form-row">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($old['message'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
