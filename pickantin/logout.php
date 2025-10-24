<?php
session_start();
session_unset();   // hapus semua session
session_destroy(); // hancurkan session

header("Location: ../form_login.php");
exit;
?>