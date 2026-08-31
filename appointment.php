<?php
require __DIR__ . '/database/connect.php';
$pageTitle = 'Book Appointment';
$doctors = $pdo->query('SELECT * FROM doctors ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$selectedDoctor = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;
session_start();
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old_input']);
$val = function ($key, $default = '') use ($old) { return htmlspecialchars($old[$key] ?? $default); };

$loggedIn = !empty($_SESSION['patient_id']);
if ($loggedIn && empty($old)) {
    $old = [
        'name' => $_SESSION['patient_name'] ?? '',
        'phone' => $_SESSION['patient_phone'] ?? '',
    ];
}

require __DIR__ . '/includes/header.php';
?>
<h1>Book an Appointment</h1>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <strong>Please fix the following:</strong>
    <ul style="margin:6px 0 0 18px;">
        <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form id="appointmentForm" method="POST" action="book.php" novalidate>
    <div class="form-row">
        <label for="name">Patient Name</label>
        <input type="text" id="name" name="name" value="<?php echo $val('name'); ?>" required>
        <div class="error-msg" id="nameError"></div>
    </div>
    <div class="form-row">
        <label for="age">Age</label>
        <input type="number" id="age" name="age" min="1" max="120" value="<?php echo $val('age'); ?>" required>
        <div class="error-msg" id="ageError"></div>
    </div>
    <div class="form-row">
        <label>Gender</label>
        <div class="radio-group">
            <label><input type="radio" name="gender" value="Male" <?php echo (($old['gender'] ?? '') === 'Male') ? 'checked' : ''; ?>> Male</label>
            <label><input type="radio" name="gender" value="Female" <?php echo (($old['gender'] ?? '') === 'Female') ? 'checked' : ''; ?>> Female</label>
            <label><input type="radio" name="gender" value="Other" <?php echo (($old['gender'] ?? '') === 'Other') ? 'checked' : ''; ?>> Other</label>
        </div>
        <div class="error-msg" id="genderError"></div>
    </div>
    <div class="form-row">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" maxlength="10" value="<?php echo $val('phone'); ?>" required>
        <div class="error-msg" id="phoneError"></div>
    </div>
    <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $val('email'); ?>" required>
        <div class="error-msg" id="emailError"></div>
    </div>
    <div class="form-row">
        <label for="doctor_id">Doctor</label>
        <select id="doctor_id" name="doctor_id" required>
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doc): $sel = ($old['doctor_id'] ?? $selectedDoctor) == $doc['doctor_id'] ? 'selected' : ''; ?>
            <option value="<?php echo (int)$doc['doctor_id']; ?>" data-dept="<?php echo htmlspecialchars($doc['department']); ?>" data-fee="<?php echo (int)$doc['fee']; ?>" <?php echo $sel; ?>>
                <?php echo htmlspecialchars($doc['name']) . ' (' . htmlspecialchars($doc['department']) . ')'; ?>
            </option>
            <?php endforeach; ?>
        </select>
        <div class="error-msg" id="doctor_idError"></div>
        <p class="meta" id="feeNote" style="margin-top:6px;"></p>
    </div>
    <div class="form-row">
        <label for="appointment_date">Date</label>
        <input type="date" id="appointment_date" name="appointment_date" value="<?php echo $val('appointment_date'); ?>" required>
        <div class="error-msg" id="appointment_dateError"></div>
    </div>
    <div class="form-row">
        <label>Time Slot</label>
        <div class="slot-grid" id="slotGrid">
            <?php foreach (TIME_SLOTS as $s): $checked = ($old['appointment_time'] ?? '') === $s ? 'checked' : ''; ?>
            <input type="radio" class="slot-radio" id="slot-<?php echo str_replace([':',' '],'',$s); ?>" name="appointment_time" value="<?php echo $s; ?>" <?php echo $checked; ?>>
            <label class="slot-chip" for="slot-<?php echo str_replace([':',' '],'',$s); ?>"><?php echo $s; ?></label>
            <?php endforeach; ?>
        </div>
        <p class="meta" id="slotHint" style="margin-top:6px;">Choose a doctor and date to see live availability.</p>
        <div class="error-msg" id="appointment_timeError"></div>
    </div>
    <div class="form-row">
        <label for="reason">Reason for Visit</label>
        <textarea id="reason" name="reason" rows="3"><?php echo $val('reason'); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Book Appointment</button>
    <button type="reset" class="btn btn-outline">Reset</button>
</form>

<script src="js/validation.js"></script>
<script src="js/slots.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
