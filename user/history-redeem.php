<?php
require_once "../auth.php";
require_once "config/db.php";

if ($_SESSION['role'] !== 'user') {
    header("Location: ../form_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT rh.*, ri.nama AS item_nama, ri.gambar, 
                 vk.nama AS vendor_nama, rh.updated_at
          FROM redemption_history rh
          JOIN redeem_items ri ON rh.item_id = ri.id
          LEFT JOIN vendorkantin vk ON rh.updated_by_vendor_id = vk.id
          WHERE rh.user_id = ? 
          ORDER BY rh.redemption_date DESC";
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
        <title>History Penukaran Kupon - ParaCanteen</title>
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

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

        <!-- Helpers -->
        <script src="../assets/vendor/js/helpers.js"></script>

        <!-- Config -->
        <script src="../assets/js/config.js"></script>

        <style>
            .table-img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 8px;
                border: 2px solid #e3e6f0;
            }
            .history-card {
                border: none;
                box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            }
            .status-badge {
                font-size: 0.8em;
                padding: 0.4em 0.8em;
            }
            .empty-state {
                padding: 3rem 1rem;
                text-align: center;
            }
            .empty-state i {
                font-size: 4rem;
                margin-bottom: 1rem;
            }
            .vendor-info {
                font-size: 0.85em;
                color: #697a8d;
            }
            .vendor-badge {
                background: #e7f3ff;
                color: #0066cc;
                padding: 0.25rem 0.5rem;
                border-radius: 4px;
                font-size: 0.75em;
            }
        </style>
    </head>
    <body>
        <!-- Layout wrapper -->
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
                                <div class="col-12">
                                    <div class="card mb-4">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <h4 class="card-title mb-0">
                                                <i class="bx bx-history me-2"></i>History Penukaran Kupon
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="card history-card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="bx bx-list-ul me-2"></i>Riwayat Penukaran
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th width="15%">Tanggal</th>
                                                            <th width="25%">Barang</th>
                                                            <th width="10%">Jumlah</th>
                                                            <th width="15%">Kupon Digunakan</th>
                                                            <th width="20%">Status</th>
                                                            <th width="15%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                        <?php if (count($redeems) === 0): ?>
                                                            <tr>
                                                                <td colspan="6" class="text-center py-5">
                                                                    <div class="empty-state">
                                                                        <i class="bx bx-package text-muted"></i>
                                                                        <h5 class="text-muted">Belum ada history penukaran</h5>
                                                                        <p class="text-muted mb-0">Anda belum pernah menukarkan kupon</p>
                                                                        <a href="redeem-kupon.php" class="btn btn-primary mt-3">
                                                                            <i class="bx bx-gift me-2"></i>Tukar Kupon Sekarang
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach ($redeems as $r): ?>
                                                                <tr>
                                                                    <td>
                                                                        <strong><?= date('d/m/Y', strtotime($r['redemption_date'])) ?></strong>
                                                                        <br>
                                                                        <small class="text-muted"><?= date('H:i', strtotime($r['redemption_date'])) ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <?php if ($r['gambar']): ?>
                                                                                <img src="../<?= $r['gambar'] ?>" alt="<?= htmlspecialchars($r['item_nama']) ?>" class="table-img me-3" onerror="this.src='../assets/img/placeholder.jpg'">
                                                                            <?php else: ?>
                                                                                <div class="table-img bg-light d-flex align-items-center justify-content-center me-3">
                                                                                    <i class="bx bx-package text-muted"></i>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div>
                                                                                <strong><?= htmlspecialchars($r['item_nama']) ?></strong>
                                                                                <br>
                                                                                <small class="text-muted">ID: #<?= $r['id'] ?></small>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-primary rounded-pill" style="font-size: 1em;">
                                                                            <?= $r['quantity'] ?> item
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-warning text-dark">
                                                                            <i class="bx bx-coin me-1"></i><?= $r['kupon_used'] ?> Kupon
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex flex-column">
                                                                            <?php if ($r['status'] === 'completed'): ?>
                                                                                <span class="badge bg-success status-badge mb-1">
                                                                                    <i class="bx bx-check-circle me-1"></i>Selesai
                                                                                </span>
                                                                                <?php if ($r['vendor_nama']): ?>
                                                                                    <small class="vendor-info">
                                                                                        <i class="bx bx-user-check me-1"></i>
                                                                                        Dikonfirmasi oleh: <?= htmlspecialchars($r['vendor_nama']) ?>
                                                                                        <br>
                                                                                        <i class="bx bx-time me-1"></i>
                                                                                        <?= date('d/m/Y H:i', strtotime($r['updated_at'])) ?>
                                                                                    </small>
                                                                                <?php endif; ?>
                                                                            <?php elseif ($r['status'] === 'pending'): ?>
                                                                                <span class="badge bg-warning status-badge mb-1">
                                                                                    <i class="bx bx-time me-1"></i>Menunggu
                                                                                </span>
                                                                                <small class="vendor-info">
                                                                                    <i class="bx bx-info-circle me-1"></i>
                                                                                    Menunggu konfirmasi vendor
                                                                                </small>
                                                                            <?php else: ?>
                                                                                <span class="badge bg-danger status-badge mb-1">
                                                                                    <i class="bx bx-x-circle me-1"></i>Dibatalkan
                                                                                </span>
                                                                                <?php if ($r['vendor_nama']): ?>
                                                                                    <small class="vendor-info">
                                                                                        <i class="bx bx-user-x me-1"></i>
                                                                                        Dibatalkan oleh: <?= htmlspecialchars($r['vendor_nama']) ?>
                                                                                        <br>
                                                                                        <i class="bx bx-time me-1"></i>
                                                                                        <?= date('d/m/Y H:i', strtotime($r['updated_at'])) ?>
                                                                                    </small>
                                                                                <?php endif; ?>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $r['id'] ?>">
                                                                            <i class="bx bx-show me-1"></i>Detail
                                                                        </button>
                                                                    </td>
                                                                </tr>

                                                                <!-- Modal Detail -->
                                                                <div class="modal fade" id="detailModal<?= $r['id'] ?>" tabindex="-1">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Detail Penukaran</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>ID Transaksi:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        #<?= $r['id'] ?>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Tanggal Penukaran:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <?= date('d/m/Y H:i', strtotime($r['redemption_date'])) ?>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Item:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <?php if ($r['gambar']): ?>
                                                                                                <img src="../<?= $r['gambar'] ?>" alt="<?= htmlspecialchars($r['item_nama']) ?>" class="table-img me-2" onerror="this.src='../assets/img/placeholder.jpg'">
                                                                                            <?php endif; ?>
                                                                                            <?= htmlspecialchars($r['item_nama']) ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Jumlah:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <?= $r['quantity'] ?> item
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Kupon Digunakan:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <span class="badge bg-warning text-dark">
                                                                                            <?= $r['kupon_used'] ?> Kupon
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Status:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <?php if ($r['status'] === 'completed'): ?>
                                                                                            <span class="badge bg-success">
                                                                                                <i class="bx bx-check-circle me-1"></i>Selesai
                                                                                            </span>
                                                                                        <?php elseif ($r['status'] === 'pending'): ?>
                                                                                            <span class="badge bg-warning">
                                                                                                <i class="bx bx-time me-1"></i>Menunggu Konfirmasi
                                                                                            </span>
                                                                                        <?php else: ?>
                                                                                            <span class="badge bg-danger">
                                                                                                <i class="bx bx-x-circle me-1"></i>Dibatalkan
                                                                                            </span>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <!-- Informasi Vendor -->
                                                                                <?php if ($r['vendor_nama']): ?>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Dikonfirmasi Oleh:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <div class="vendor-badge">
                                                                                            <i class="bx bx-user-check me-1"></i>
                                                                                            <?= htmlspecialchars($r['vendor_nama']) ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Waktu Konfirmasi:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <small class="text-muted">
                                                                                            <i class="bx bx-time me-1"></i>
                                                                                            <?= date('d/m/Y H:i', strtotime($r['updated_at'])) ?>
                                                                                        </small>
                                                                                    </div>
                                                                                </div>
                                                                                <?php elseif ($r['status'] === 'pending'): ?>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-4">
                                                                                        <strong>Status:</strong>
                                                                                    </div>
                                                                                    <div class="col-8">
                                                                                        <small class="text-info">
                                                                                            <i class="bx bx-info-circle me-1"></i>
                                                                                            Menunggu konfirmasi dari vendor kantin
                                                                                        </small>
                                                                                    </div>
                                                                                </div>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
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
        <script>
            // Auto-hide alerts if any
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);
            });
        </script>
    </body>
</html>