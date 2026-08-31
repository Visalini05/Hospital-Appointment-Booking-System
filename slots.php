<?php
// slots.php - AJAX endpoint: returns which time slots are already booked for a doctor+date.
require __DIR__ . '/database/connect.php';
header('Content-Type: application/json');

$doctorId = (int)($_GET['doctor_id'] ?? 0);
$date = trim($_GET['date'] ?? '');

if ($doctorId <= 0 || $date === '') {
    echo json_encode(['taken' => []]);
    exit;
}

$stmt = $pdo->prepare('SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status != "Cancelled"');
$stmt->execute([$doctorId, $date]);
$taken = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'appointment_time');

echo json_encode(['taken' => $taken]);
