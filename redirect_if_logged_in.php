<?php
// redirect_if_logged_in.php
session_start();

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'user':
            header("Location: user/");
            exit;
        case 'vendorkantin':
            header("Location: vendorkantin/");
            exit;
        case 'admin':
            header("Location: admin/");
            exit;
        case 'pickantin':
            header("Location: pickantin/");
            exit;    
    }
}
?>