<?php
// book.php - server-side validation and insertion (PHP re-validates even though JS already checked).
session_start();
require __DIR__ . '/database/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: appointment.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$age = trim($_POST['age'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$doctorId = (int)($_POST['doctor_id'] ?? 0);
$date = trim($_POST['appointment_date'] ?? '');
$time = trim($_POST['appointment_time'] ?? '');
$reason = trim($_POST['reason'] ?? '');

$errors = [];

if ($name === '' || !preg_match('/^[A-Za-z ]{2,50}$/', $name)) {
    $errors[] = 'Please enter a valid name (letters only, 2-50 characters).';
}
if ($age === '' || !ctype_digit($age) || (int)$age < 1 || (int)$age > 120) {
    $errors[] = 'Please enter a valid age between 1 and 120.';
}
if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
    $errors[] = 'Please select a gender.';
}
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    $errors[] = 'Phone number must be exactly 10 digits.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if ($doctorId <= 0) {
    $errors[] = 'Please select a doctor.';
} else {
    $check = $pdo->prepare('SELECT COUNT(*) FROM doctors WHERE doctor_id = ?');
    $check->execute([$doctorId]);
    if ((int)$check->fetchColumn() === 0) {
        $errors[] = 'Selected doctor does not exist.';
    }
}
$today = date('Y-m-d');
if ($date === '' || $date < $today) {
    $errors[] = 'Appointment date cannot be empty or in the past.';
}
if ($time === '') {
    $errors[] = 'Please select a time slot.';
}

// Prevent double-booking the same doctor at the same date/time.
if (empty($errors)) {
    $dupe = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != "Cancelled"');
    $dupe->execute([$doctorId, $date, $time]);
    if ((int)$dupe->fetchColumn() > 0) {
        $errors[] = 'This doctor is already booked at the selected date and time. Please choose another slot.';
    }
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: appointment.php');
    exit;
}

// Insert (or reuse) the patient record, then the appointment.
$pdo->beginTransaction();
try {
    $pStmt = $pdo->prepare('INSERT INTO patients (name, age, gender, phone, email) VALUES (?, ?, ?, ?, ?)');
    $pStmt->execute([$name, (int)$age, $gender, $phone, $email]);
    $patientId = (int)$pdo->lastInsertId();

    $aStmt = $pdo->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $aStmt->execute([$patientId, $doctorId, $date, $time, $reason, 'Confirmed', date('Y-m-d H:i:s')]);
    $appointmentId = (int)$pdo->lastInsertId();

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['form_errors'] = ['Something went wrong while saving your appointment. Please try again.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: appointment.php');
    exit;
}

header('Location: confirmation.php?id=' . $appointmentId);
exit;
