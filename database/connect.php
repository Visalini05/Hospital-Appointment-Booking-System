<?php
// database/connect.php - PDO SQLite connection, schema creation, migrations, and seed data.

$dbPath = __DIR__ . '/hospital.db';
$isNew = !file_exists($dbPath);

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// List of departments used across the site (search filters, services page, seed data).
const DEPARTMENTS = ['Cardiology', 'Neurology', 'Pediatrics', 'Orthopedics', 'Dermatology', 'General Medicine'];

// ---------- Core tables ----------
$pdo->exec("CREATE TABLE IF NOT EXISTS doctors (
    doctor_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    department TEXT NOT NULL,
    qualification TEXT,
    experience INTEGER,
    phone TEXT,
    email TEXT,
    available_days TEXT,
    available_time TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS patients (
    patient_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    age INTEGER,
    gender TEXT,
    phone TEXT NOT NULL,
    email TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INTEGER NOT NULL,
    doctor_id INTEGER NOT NULL,
    appointment_date TEXT NOT NULL,
    appointment_time TEXT NOT NULL,
    reason TEXT,
    status TEXT NOT NULL DEFAULT 'Confirmed',
    created_at TEXT NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id)
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS admin (
    admin_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
)");

// ---------- New tables ----------
$pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
    review_id INTEGER PRIMARY KEY AUTOINCREMENT,
    doctor_id INTEGER NOT NULL,
    patient_id INTEGER,
    appointment_id INTEGER UNIQUE,
    patient_name TEXT NOT NULL,
    rating INTEGER NOT NULL,
    comment TEXT,
    status TEXT NOT NULL DEFAULT 'Pending',
    created_at TEXT NOT NULL,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id)
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS messages (
    message_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    subject TEXT,
    message TEXT NOT NULL,
    is_read INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL
)");

// ---------- Lightweight migrations (safe to re-run) ----------
function addColumnIfMissing(PDO $pdo, string $table, string $column, string $definition): void
{
    $cols = $pdo->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        if (strcasecmp($c['name'], $column) === 0) {
            return; // already exists
        }
    }
    $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
}

addColumnIfMissing($pdo, 'patients', 'password', 'TEXT');
addColumnIfMissing($pdo, 'patients', 'created_at', 'TEXT');
addColumnIfMissing($pdo, 'doctors', 'bio', 'TEXT');
addColumnIfMissing($pdo, 'doctors', 'fee', 'INTEGER');

// ---------- Seed doctors ----------
$count = (int)$pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn();
if ($count === 0) {
    $doctors = [
        ['Dr. Priya Sharma', 'Cardiology', 'MD, DM Cardiology', 8, '9876500001', 'priya.sharma@hospital.com', 'Mon-Sat', '09:00 AM - 05:00 PM',
            'Dr. Priya Sharma specializes in interventional cardiology and preventive heart care, helping patients manage and reverse cardiovascular risk.', 600],
        ['Dr. Arun Kumar', 'Neurology', 'MD, DM Neurology', 12, '9876500002', 'arun.kumar@hospital.com', 'Mon-Fri', '10:00 AM - 04:00 PM',
            'Dr. Arun Kumar treats disorders of the brain, spine and nervous system, with a special interest in migraine and stroke recovery.', 700],
        ['Dr. Meera Nair', 'Pediatrics', 'MD Pediatrics', 6, '9876500003', 'meera.nair@hospital.com', 'Mon-Sat', '09:00 AM - 01:00 PM',
            'Dr. Meera Nair is a warm, patient-first pediatrician focused on child development, immunization and everyday childhood illnesses.', 400],
        ['Dr. Rajesh Iyer', 'Orthopedics', 'MS Ortho', 10, '9876500004', 'rajesh.iyer@hospital.com', 'Tue-Sun', '11:00 AM - 06:00 PM',
            'Dr. Rajesh Iyer specializes in joint replacement, sports injuries and spine care, combining surgery with physiotherapy-led recovery.', 550],
        ['Dr. Fatima Khan', 'Dermatology', 'MD Dermatology', 5, '9876500005', 'fatima.khan@hospital.com', 'Mon-Fri', '10:00 AM - 03:00 PM',
            'Dr. Fatima Khan treats skin, hair and nail conditions, from acne and eczema to cosmetic dermatology consultations.', 500],
        ['Dr. Karthik Rao', 'General Medicine', 'MBBS, MD', 15, '9876500006', 'karthik.rao@hospital.com', 'Mon-Sat', '08:00 AM - 02:00 PM',
            'Dr. Karthik Rao is a general physician handling everyday illness, chronic disease management and full-body health checkups.', 300],
    ];
    $stmt = $pdo->prepare('INSERT INTO doctors (name, department, qualification, experience, phone, email, available_days, available_time, bio, fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($doctors as $d) {
        $stmt->execute($d);
    }
} else {
    // Backfill bio/fee for existing rows created before this update.
    $need = $pdo->query('SELECT COUNT(*) FROM doctors WHERE bio IS NULL OR bio = ""')->fetchColumn();
    if ((int)$need > 0) {
        $pdo->exec("UPDATE doctors SET bio = COALESCE(NULLIF(bio,''), name || ' is an experienced ' || department || ' specialist at ABC Hospital, dedicated to attentive, evidence-based patient care.') WHERE bio IS NULL OR bio = ''");
        $pdo->exec("UPDATE doctors SET fee = 500 WHERE fee IS NULL");
    }
}

// ---------- Seed a default admin account ----------
$adminCount = (int)$pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
if ($adminCount === 0) {
    $stmt = $pdo->prepare('INSERT INTO admin (username, password) VALUES (?, ?)');
    $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
}

require_once __DIR__ . '/../includes/functions.php';
