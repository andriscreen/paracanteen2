<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once dirname(__DIR__) . '/../auth.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../form_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../account-settings.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$conn->begin_transaction();
try {
    // 1) Hapus order_menus milik user (melalui join ke orders)
    if ($stmt = $conn->prepare('DELETE om FROM order_menus om JOIN orders o ON om.order_id = o.id WHERE o.user_id = ?')) {
        $stmt->bind_param('i', $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Gagal hapus order_menus: ' . $stmt->error);
        }
        $stmt->close();
    }

    // 2) Hapus orders milik user
    if ($stmt = $conn->prepare('DELETE FROM orders WHERE user_id = ?')) {
        $stmt->bind_param('i', $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Gagal hapus orders: ' . $stmt->error);
        }
        $stmt->close();
    }

    // 3) Hapus user
    if ($stmt = $conn->prepare('DELETE FROM users WHERE id = ? LIMIT 1')) {
        $stmt->bind_param('i', $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Gagal hapus user: ' . $stmt->error);
        }
        $stmt->close();
    }

    $conn->commit();

    // Hancurkan sesi dan redirect ke login
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();

    header('Location: ../../form_login.php');
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    header('Location: ../account-settings.php?error=' . urlencode('Gagal menghapus akun: ' . $e->getMessage()));
    exit;
}
