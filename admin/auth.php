<?php
// admin/auth.php - include at the top of every protected admin page.
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
