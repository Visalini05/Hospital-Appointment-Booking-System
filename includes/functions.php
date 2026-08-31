<?php
// includes/functions.php - small shared helpers used across the site.

const TIME_SLOTS = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'];

function formatApptId(int $id): string
{
    return 'APT' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
}

function doctorAvatarInitials(string $name): string
{
    $clean = trim(str_ireplace('Dr.', '', $name));
    $parts = preg_split('/\s+/', $clean);
    $initials = '';
    foreach ($parts as $p) {
        if ($p !== '') { $initials .= strtoupper($p[0]); }
        if (strlen($initials) >= 2) break;
    }
    return $initials !== '' ? $initials : 'DR';
}

// Deterministic gradient (from a small fixed palette) so the same doctor always gets the same colour.
function doctorAvatarColor(int $doctorId): string
{
    $palette = [
        'linear-gradient(135deg,#2563EB,#4F46E5)',
        'linear-gradient(135deg,#0D9488,#0891B2)',
        'linear-gradient(135deg,#7C3AED,#C026D3)',
        'linear-gradient(135deg,#DB2777,#F97316)',
        'linear-gradient(135deg,#059669,#10B981)',
        'linear-gradient(135deg,#EA580C,#DC2626)',
    ];
    return $palette[$doctorId % count($palette)];
}

function ratingStars(float $avg): string
{
    $full = (int)round($avg);
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $full ? '★' : '☆';
    }
    return $out;
}

function doctorRating(PDO $pdo, int $doctorId): array
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c, AVG(rating) AS a FROM reviews WHERE doctor_id = ? AND status = 'Approved'");
    $stmt->execute([$doctorId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = (int)($row['c'] ?? 0);
    $avg = $count > 0 ? round((float)$row['a'], 1) : 0.0;
    return ['count' => $count, 'avg' => $avg];
}

function flash(string $key, ?string $value = null)
{
    if ($value !== null) {
        $_SESSION[$key] = $value;
        return null;
    }
    $v = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);
    return $v;
}

function currentPatientId(): ?int
{
    return $_SESSION['patient_id'] ?? null;
}
