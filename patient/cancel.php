<?php
// patient/cancel.php - lets a patient cancel their own appointment (verified by matching phone number).
require __DIR__ . '/../database/connect.php';

$appointmentId = (int)($_POST['appointment_id'] ?? 0);
$phone = trim($_POST['phone'] ?? '');

if ($appointmentId > 0 && $phone !== '') {
    $stmt = $pdo->prepare('SELECT a.appointment_id FROM appointments a
        JOIN patients p ON p.patient_id = a.patient_id
        WHERE a.appointment_id = ? AND p.phone = ?');
    $stmt->execute([$appointmentId, $phone]);
    if ($stmt->fetch()) {
        $upd = $pdo->prepare('UPDATE appointments SET status = ? WHERE appointment_id = ?');
        $upd->execute(['Cancelled', $appointmentId]);
    }
}

header('Location: dashboard.php?phone=' . urlencode($phone) . '&msg=cancelled');
exit;
