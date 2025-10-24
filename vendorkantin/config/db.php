<?php
// Database connection for paragonapp
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'paragonapp';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
// Usage: include 'config/db.php';
?>
