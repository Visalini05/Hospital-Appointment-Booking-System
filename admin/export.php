<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/../database/connect.php';

$rows = $pdo->query('SELECT a.appointment_id, p.name AS patient_name, p.phone, p.email, d.name AS doctor_name,
    d.department, a.appointment_date, a.appointment_time, a.status, a.reason, a.created_at
    FROM appointments a
    JOIN patients p ON p.patient_id = a.patient_id
    JOIN doctors d ON d.doctor_id = a.doctor_id
    ORDER BY a.appointment_id')->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Appointment ID', 'Patient Name', 'Phone', 'Email', 'Doctor', 'Department', 'Date', 'Time', 'Status', 'Reason', 'Booked At']);
foreach ($rows as $r) {
    fputcsv($out, [
        formatApptId((int)$r['appointment_id']), $r['patient_name'], $r['phone'], $r['email'],
        $r['doctor_name'], $r['department'], $r['appointment_date'], $r['appointment_time'],
        $r['status'], $r['reason'], $r['created_at'],
    ]);
}
fclose($out);
exit;
