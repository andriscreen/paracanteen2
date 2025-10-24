<?php
require_once "../auth.php";
require_once "config/db.php";

if ($_SESSION['role'] !== 'user') {
    header("Location: ../form_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT rh.*, ri.nama AS item_nama, ri.gambar FROM redemption_history rh
          JOIN redeem_items ri ON rh.item_id = ri.id
          WHERE rh.user_id = ? ORDER BY rh.redemption_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$redeems = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>History Penukaran Kupon</title>
        <meta name="description" content="" />
        
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="../assets/css/demo.css" />
        <link rel="stylesheet" href="../assets/css/redeem-kupon.css" />

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>
        <script src="../assets/vendor/js/helpers.js"></script>
        <script src="../assets/js/config.js"></script>
    </head>
    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <!-- Menu -->
                <?php include 'layout/sidebar.php'; ?>
                <!-- / Menu -->
                <div class="layout-page">
                    <!-- Navbar -->
                    <?php include 'layout/navbar.php'; ?>
                    <!-- / Navbar -->
                    <div class="content-wrapper">
                        <!-- Content -->
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <h4 class="fw-bold py-3 mb-4">
                                <span class="text-muted fw-light">User /</span> History Penukaran Kupon
                            </h4>
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle mb-3">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Barang</th>
                                                    <th>Jumlah</th>
                                                    <th>Kupon Digunakan</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($redeems) === 0): ?>
                                                    <tr><td colspan="5" class="text-center">Belum ada penukaran kupon.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($redeems as $r): ?>
                                                        <tr>
                                                            <td><?= date('d-m-Y H:i', strtotime($r['redemption_date'])) ?></td>
                                                            <td>
                                                                <img src="<?= $r['gambar'] ?>" alt="<?= htmlspecialchars($r['item_nama']) ?>" style="width:40px;height:40px;border-radius:6px;margin-right:8px;vertical-align:middle;">
                                                                <?= htmlspecialchars($r['item_nama']) ?>
                                                            </td>
                                                            <td><?= $r['quantity'] ?></td>
                                                            <td><?= $r['kupon_used'] ?></td>
                                                            <td>
                                                                <?php if ($r['status'] === 'completed'): ?>
                                                                    <span class="badge bg-success">Selesai</span>
                                                                <?php elseif ($r['status'] === 'pending'): ?>
                                                                    <span class="badge bg-warning">Menunggu</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">Dibatalkan</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
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
                                    <script>document.write(new Date().getFullYear());</script>
                                    , Part of
                                    <a href="#" target="_blank" class="footer-link fw-bolder">ParagonCorp</a>
                                </div>
                            </div>
                        </footer>
                        <!-- / Footer -->
                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- Content wrapper -->
                </div>
                <!-- / Layout page -->
            </div>
            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <!-- / Layout wrapper -->
        <!-- Core JS -->
        <script src="../assets/vendor/libs/jquery/jquery.js"></script>
        <script src="../assets/vendor/libs/popper/popper.js"></script>
        <script src="../assets/vendor/js/bootstrap.js"></script>
        <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="../assets/vendor/js/menu.js"></script>
        <!-- Vendors JS -->
        <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
        <!-- Main JS -->
        <script src="../assets/js/main.js"></script>
        <!-- Page JS -->
        <script src="../assets/js/dashboards-analytics.js"></script>
        <script async defer src="https://buttons.github.io/buttons.js"></script>
    </body>
</html>
