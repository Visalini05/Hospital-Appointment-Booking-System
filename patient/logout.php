<?php
session_start();
unset($_SESSION['patient_id'], $_SESSION['patient_name'], $_SESSION['patient_phone']);
header('Location: login.php');
exit;
