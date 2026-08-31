<?php
// includes/header.php - shared top navigation. Include $pageTitle before this file.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($pageTitle)) { $pageTitle = 'ABC Hospital'; }
$base = isset($inSubfolder) && $inSubfolder ? '..' : '.';
$patientName = $_SESSION['patient_name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> - ABC Hospital</title>
<link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body>
<header class="navbar" id="siteNavbar">
    <div class="nav-inner">
        <a class="logo" href="<?php echo $base; ?>/index.php">
            <span class="logo-icon"></span>
            <span class="logo-text">ABC <span>Hospital</span></span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav id="siteNav">
            <a href="<?php echo $base; ?>/index.php">Home</a>
            <a href="<?php echo $base; ?>/services.php">Services</a>
            <a href="<?php echo $base; ?>/doctors.php">Doctors</a>
            <a href="<?php echo $base; ?>/appointment.php">Book Appointment</a>
            <a href="<?php echo $base; ?>/patient/dashboard.php">My Appointments</a>
            <a href="<?php echo $base; ?>/about.php">About</a>
            <a href="<?php echo $base; ?>/contact.php">Contact</a>
            <?php if ($patientName): ?>
                <a href="<?php echo $base; ?>/patient/dashboard.php" class="nav-admin">Hi, <?php echo htmlspecialchars(explode(' ', $patientName)[0]); ?></a>
                <a href="<?php echo $base; ?>/patient/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base; ?>/patient/login.php" class="nav-admin">Patient Login</a>
            <?php endif; ?>
            <a href="<?php echo $base; ?>/admin/login.php">Admin</a>
        </nav>
    </div>
</header>

<script>
(function () {
    var toggle = document.getElementById('navToggle');
    var nav = document.getElementById('siteNav');
    var navbar = document.getElementById('siteNavbar');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('nav-open');
            toggle.classList.toggle('open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('nav-open');
                toggle.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }
    if (navbar) {
        window.addEventListener('scroll', function () {
            navbar.classList.toggle('scrolled', window.scrollY > 8);
        });
    }
})();
</script>

<main class="container">
