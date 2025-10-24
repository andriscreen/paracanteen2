<?php
// auth.php
session_start();

// Jika belum login, paksa kembali ke login
if (!isset($_SESSION['role'])) {
    header("Location: ../form_login.php");
    exit;
}
?>