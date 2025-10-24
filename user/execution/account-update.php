<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once dirname(__DIR__) . '/../auth.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth-login-basic.html');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../account-settings.php');
    exit;
}

function e_in(string $s): string { return trim($s); }

$nama       = isset($_POST['nama']) ? e_in($_POST['nama']) : '';
$nip        = isset($_POST['nip']) ? e_in($_POST['nip']) : '';
$gmail      = isset($_POST['gmail']) ? e_in($_POST['gmail']) : '';
$departemen = isset($_POST['departemen']) ? e_in($_POST['departemen']) : '';

$errors = [];

// Basic validations
if ($nama === '') { $errors[] = 'Nama wajib diisi.'; }
if ($gmail !== '' && !filter_var($gmail, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Format email tidak valid.'; }

// Handle avatar upload if provided
$avatarDbPath = null; // keep null to avoid overwrite if no new file
if (isset($_FILES['upload']) && is_array($_FILES['upload']) && $_FILES['upload']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['upload'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Gagal mengunggah file (kode: ' . (int)$file['error'] . ').';
    } else {
        $maxSize = 2 * 1024 * 1024; // 2 MB
        if ($file['size'] > $maxSize) {
            $errors[] = 'Ukuran file melebihi 2MB.';
        }
        // Validate MIME and extension
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
        ];
        if (!isset($allowed[$mime])) {
            $errors[] = 'Tipe file tidak diizinkan. Hanya JPG, PNG, GIF.';
        }

        if (!$errors) {
            $ext = $allowed[$mime];
            $safeBase = 'avatar_u' . $user_id . '_' . date('YmdHis');
            $newFilename = $safeBase . '.' . $ext;

            // Destination directory (projectRoot/assets/img/avatars)
            $destDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'avatars';
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0775, true);
            }
            $destPath = $destDir . DIRECTORY_SEPARATOR . $newFilename;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                $errors[] = 'Gagal memindahkan file upload.';
            } else {
                // Store DB path relative: assets/img/avatars/filename.ext
                $avatarDbPath = 'assets/img/avatars/' . $newFilename;
            }
        }
    }
}

if ($errors) {
    $q = http_build_query(['error' => implode(' ', $errors)]);
    header('Location: ../account-settings.php?' . $q);
    exit;
}

// Build dynamic update (do not overwrite avatar if not uploaded)
$fields = ['`nama` = ?', '`nip` = ?', '`gmail` = ?', '`departemen` = ?'];
$params = [$nama, $nip, $gmail, $departemen];
$types  = 'ssss';
if ($avatarDbPath !== null) {
    $fields[] = '`avatars` = ?';
    $params[] = $avatarDbPath;
    $types   .= 's';
}
$params[] = $user_id;
$types    .= 'i';

$sql = 'UPDATE `users` SET ' . implode(', ', $fields) . ' WHERE `id` = ? LIMIT 1';

$conn->begin_transaction();
try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    $stmt->close();
    $conn->commit();
    header('Location: ../account-settings.php?updated=1');
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    $q = http_build_query(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
    header('Location: ../account-settings.php?' . $q);
    exit;
}
