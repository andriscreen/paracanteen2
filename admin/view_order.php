<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; ?>

<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Order ID tidak valid!";
    header("Location: manage-user-order.php");
    exit;
}

$order_id = (int)$_GET['id'];

// Query untuk mendapatkan detail order
$query = "SELECT o.*, u.nama as user_nama, u.nip, u.departemen, u.gmail, u.rfid, 
                 w.week_number, y.year_value, p.name as plant_name, 
                 pl.name as place_name, s.nama_shift
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          LEFT JOIN week w ON o.week_id = w.id 
          LEFT JOIN year y ON o.year_id = y.id 
          LEFT JOIN plant p ON o.plant_id = p.id 
          LEFT JOIN place pl ON o.place_id = pl.id 
          LEFT JOIN shift s ON o.shift_id = s.id 
          WHERE o.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order tidak ditemukan!";
    header("Location: manage-user-order.php");
    exit;
}

// Fungsi untuk mendapatkan status hari
function getDayStatus($order_data, $day) {
    $day_lower = strtolower($day);
    
    if ($order_data["libur_$day_lower"] == 1) {
        return ['type' => 'libur', 'label' => 'Libur', 'class' => 'bg-secondary'];
    } elseif ($order_data["makan_$day_lower"] == 1) {
        return ['type' => 'makan', 'label' => 'Makan', 'class' => 'bg-success'];
    } elseif ($order_data["kupon_$day_lower"] == 1) {
        return ['type' => 'kupon', 'label' => 'Kupon', 'class' => 'bg-warning text-dark'];
    } else {
        return ['type' => 'none', 'label' => 'Tidak Ada', 'class' => 'bg-light text-muted'];
    }
}

// Hitung total
$total_makan = 0;
$total_kupon = 0;
$total_libur = 0;
$days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

foreach ($days as $day) {
    if ($order["makan_$day"] == 1) $total_makan++;
    if ($order["kupon_$day"] == 1) $total_kupon++;
    if ($order["libur_$day"] == 1) $total_libur++;
}
?>

<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Detail Order - ParaCanteen</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <style>
        .day-card { transition: all 0.3s ease; }
        .day-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .status-badge { font-size: 0.8em; }
        .total-card { border-left: 4px solid; }
        .total-makan { border-left-color: #28a745; }
        .total-kupon { border-left-color: #ffc107; }
        .total-libur { border-left-color: #6c757d; }
    </style>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include 'layout/sidebar.php'; ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include 'layout/navbar.php'; ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-lg-12 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0">
                                            <i class="bx bx-show"></i> Detail Order #<?= $order['id'] ?>
                                        </h4>
                                        <a href="manage-user-order.php" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <!-- Alert messages -->
                                        <?php if (isset($_SESSION['success'])): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_SESSION['error'])): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Informasi User -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0"><i class="bx bx-user"></i> Informasi User</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table table-borderless">
                                                            <tr>
                                                                <td width="40%"><strong>Nama</strong></td>
                                                                <td><?= htmlspecialchars($order['user_nama']) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>NIP</strong></td>
                                                                <td><?= htmlspecialchars($order['nip']) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Departemen</strong></td>
                                                                <td>
                                                                    <?php if (!empty($order['departemen'])): ?>
                                                                        <span class="badge bg-primary"><?= htmlspecialchars($order['departemen']) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Email</strong></td>
                                                                <td><?= htmlspecialchars($order['gmail']) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>RFID</strong></td>
                                                                <td><?= $order['rfid'] ? htmlspecialchars($order['rfid']) : '<span class="text-muted">-</span>' ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header bg-info text-white">
                                                        <h6 class="mb-0"><i class="bx bx-calendar"></i> Informasi Order</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table table-borderless">
                                                            <tr>
                                                                <td width="40%"><strong>Minggu</strong></td>
                                                                <td>Minggu <?= $order['week_number'] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Tahun</strong></td>
                                                                <td><?= $order['year_value'] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Plant</strong></td>
                                                                <td><?= htmlspecialchars($order['plant_name']) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Place</strong></td>
                                                                <td><?= htmlspecialchars($order['place_name']) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Shift</strong></td>
                                                                <td>
                                                                    <?php if ($order['nama_shift']): ?>
                                                                        <span class="badge bg-info"><?= $order['nama_shift'] ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Tanggal Order</strong></td>
                                                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ringkasan Total -->
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="card total-card total-makan">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-success"><?= $total_makan ?></h3>
                                                        <p class="mb-0">Hari Makan</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card total-card total-kupon">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-warning"><?= $total_kupon ?></h3>
                                                        <p class="mb-0">Hari Kupon</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card total-card total-libur">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-secondary"><?= $total_libur ?></h3>
                                                        <p class="mb-0">Hari Libur</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detail Per Hari -->
                                        <div class="card">
                                            <div class="card-header bg-secondary text-white">
                                                <h6 class="mb-0"><i class="bx bx-grid-alt"></i> Detail Per Hari</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <?php 
                                                    $days_indonesia = [
                                                        'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 
                                                        'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 
                                                        'minggu' => 'Minggu'
                                                    ];
                                                    
                                                    foreach ($days_indonesia as $day_en => $day_id): 
                                                        $status = getDayStatus($order, $day_en);
                                                    ?>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="card day-card">
                                                            <div class="card-body text-center">
                                                                <h6 class="card-title"><?= $day_id ?></h6>
                                                                <span class="badge status-badge <?= $status['class'] ?>">
                                                                    <?= $status['label'] ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tombol Aksi -->
                                        <div class="row mt-4">
                                            <div class="col-12 text-center">
                                                <a href="edit_order.php?id=<?= $order['id'] ?>" class="btn btn-warning">
                                                    <i class="bx bx-edit"></i> Edit Order
                                                </a>
                                                <a href="manage-user-order.php" class="btn btn-secondary">
                                                    <i class="bx bx-arrow-back"></i> Kembali ke Daftar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                , Part of
                                <a href="#" target="_blank" class="footer-link fw-bolder">ParagonCorp</a>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>