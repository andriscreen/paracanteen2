<?php
include "../auth.php";
if ($_SESSION['role'] !== 'vendorkantin') { 
    header("Location: ../form_login.php"); 
    exit; 
} 
include 'config/db.php'; 

// Inisialisasi variabel
$success = '';
$error = '';
$redemptions = [];
$search_query = '';

// Handle search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
}

// Update status penukaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $redemption_id = (int)$_POST['redemption_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $vendor_id = $_SESSION['user_id']; // ID vendor yang sedang login
    
    try {
        // Mulai transaction
        $conn->begin_transaction();
        
        // Ambil data redemption untuk mendapatkan user_id dan kupon_used
        $sql_select = "SELECT user_id, kupon_used FROM redemption_history WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param("i", $redemption_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $redemption_data = $result->fetch_assoc();
        $stmt_select->close();
        
        if (!$redemption_data) {
            throw new Exception("Data penukaran tidak ditemukan!");
        }
        
        $user_id = $redemption_data['user_id'];
        $kupon_used = $redemption_data['kupon_used'];
        
        // Update status penukaran
        $sql_update = "UPDATE redemption_history SET status = ?, updated_by_vendor_id = ?, updated_at = NOW() WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sii", $status, $vendor_id, $redemption_id);
        
        if (!$stmt_update->execute()) {
            throw new Exception("Gagal mengupdate status penukaran!");
        }
        $stmt_update->close();
        
        // Jika status dibatalkan, kembalikan kupon ke user
        if ($status === 'cancelled') {
            $sql_refund = "UPDATE users SET total_kupon = total_kupon + ? WHERE id = ?";
            $stmt_refund = $conn->prepare($sql_refund);
            $stmt_refund->bind_param("ii", $kupon_used, $user_id);
            
            if (!$stmt_refund->execute()) {
                throw new Exception("Gagal mengembalikan kupon ke user!");
            }
            $stmt_refund->close();
            
            $success = "Status penukaran berhasil diupdate dan kupon telah dikembalikan ke user!";
        } else {
            $success = "Status penukaran berhasil diupdate!";
        }
        
        // Commit transaction
        $conn->commit();
        
    } catch (Exception $e) {
        // Rollback transaction jika ada error
        $conn->rollback();
        $error = "Error: " . $e->getMessage();
    }
}

// Build query dengan search
$sql = "SELECT rh.*, u.nama AS user_nama, u.nip, ri.nama AS item_nama, ri.gambar 
        FROM redemption_history rh 
        JOIN users u ON rh.user_id = u.id 
        JOIN redeem_items ri ON rh.item_id = ri.id";

if (!empty($search_query)) {
    $sql .= " WHERE u.nama LIKE '%$search_query%' OR u.nip LIKE '%$search_query%' OR ri.nama LIKE '%$search_query%'";
}

$sql .= " ORDER BY rh.redemption_date DESC";

$result = $conn->query($sql);

if ($result) {
    $redemptions = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $error = "Gagal mengambil data: " . $conn->error;
}

// Hitung statistik
$stats_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
              FROM redemption_history";

if (!empty($search_query)) {
    $stats_sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN rh.status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN rh.status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN rh.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                  FROM redemption_history rh
                  JOIN users u ON rh.user_id = u.id 
                  JOIN redeem_items ri ON rh.item_id = ri.id
                  WHERE u.nama LIKE '%$search_query%' OR u.nip LIKE '%$search_query%' OR ri.nama LIKE '%$search_query%'";
}

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Kelola Penukaran Kupon - ParaCanteen</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

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
      .stats-card {
        transition: transform 0.2s;
      }
      .stats-card:hover {
        transform: translateY(-2px);
      }
      .badge-status {
        font-size: 0.8em;
        padding: 0.4em 0.8em;
      }
      .action-buttons .btn {
        padding: 0.375rem 0.75rem;
      }
      .search-box {
        max-width: 400px;
      }
      .results-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
      }
      .refund-info {
        background: #e7f3ff;
        border-left: 4px solid #0066cc;
        padding: 0.75rem;
        border-radius: 4px;
        margin-top: 0.5rem;
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
                        <i class="bx bx-refresh me-2"></i>Kelola Penukaran Kupon
                      </h4>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notifikasi -->
              <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="bx bx-check-circle me-2"></i><?= $success ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>
              
              <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="bx bx-error-circle me-2"></i><?= $error ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <!-- Search Box -->
              <div class="row mb-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <form method="GET" action="" class="row g-3 align-items-center">
                        <div class="col-md-8">
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bx bx-search"></i>
                            </span>
                            <input 
                              type="text" 
                              class="form-control" 
                              name="search" 
                              placeholder="Cari berdasarkan nama user, NIP, atau nama item..." 
                              value="<?= htmlspecialchars($search_query) ?>"
                            >
                          </div>
                        </div>
                        <div class="col-md-2">
                          <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search me-2"></i>Cari
                          </button>
                        </div>
                        <div class="col-md-2">
                          <?php if (!empty($search_query)): ?>
                            <a href="redemption-list.php" class="btn btn-outline-secondary w-100">
                              <i class="bx bx-x me-2"></i>Clear
                            </a>
                          <?php endif; ?>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Info Hasil Pencarian -->
              <?php if (!empty($search_query)): ?>
                <div class="row mb-4">
                  <div class="col-12">
                    <div class="results-info">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Hasil Pencarian untuk: <strong>"<?= htmlspecialchars($search_query) ?>"</strong></h6>
                          <p class="mb-0 text-muted">Ditemukan <?= count($redemptions) ?> penukaran</p>
                        </div>
                        <a href="redemption-list.php" class="btn btn-sm btn-outline-primary">
                          <i class="bx bx-list-ul me-1"></i>Tampilkan Semua
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Statistik -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <div class="card stats-card border-left-primary">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                          <h5 class="card-title text-muted mb-0">Total Penukaran</h5>
                          <h3 class="fw-bold mb-0"><?= $stats['total'] ?? 0 ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                          <i class="bx bx-refresh text-primary" style="font-size: 2rem;"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card stats-card border-left-warning">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                          <h5 class="card-title text-muted mb-0">Menunggu</h5>
                          <h3 class="fw-bold mb-0"><?= $stats['pending'] ?? 0 ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                          <i class="bx bx-time text-warning" style="font-size: 2rem;"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card stats-card border-left-success">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                          <h5 class="card-title text-muted mb-0">Selesai</h5>
                          <h3 class="fw-bold mb-0"><?= $stats['completed'] ?? 0 ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                          <i class="bx bx-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card stats-card border-left-danger">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                          <h5 class="card-title text-muted mb-0">Dibatalkan</h5>
                          <h3 class="fw-bold mb-0"><?= $stats['cancelled'] ?? 0 ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                          <i class="bx bx-x-circle text-danger" style="font-size: 2rem;"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Daftar Penukaran -->
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2"></i>Daftar Permintaan Penukaran
                        <?php if (!empty($search_query)): ?>
                          <small class="text-muted">(Hasil Pencarian)</small>
                        <?php endif; ?>
                      </h5>
                      <div class="d-flex gap-2">
                        <?php if (!empty($search_query)): ?>
                          <span class="badge bg-info">Pencarian Aktif</span>
                        <?php endif; ?>
                        <span class="badge bg-primary">Total: <?= count($redemptions) ?></span>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-striped table-hover">
                          <thead>
                            <tr>
                              <th width="5%">#</th>
                              <th width="15%">Tanggal</th>
                              <th width="20%">User</th>
                              <th width="20%">Item</th>
                              <th width="10%">Qty</th>
                              <th width="10%">Kupon</th>
                              <th width="10%">Status</th>
                              <th width="10%">Aksi</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (empty($redemptions)): ?>
                              <tr>
                                <td colspan="8" class="text-center py-4">
                                  <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-search text-muted mb-2" style="font-size: 3rem;"></i>
                                    <p class="text-muted mb-0">
                                      <?php if (!empty($search_query)): ?>
                                        Tidak ditemukan penukaran untuk "<?= htmlspecialchars($search_query) ?>"
                                      <?php else: ?>
                                        Tidak ada data penukaran
                                      <?php endif; ?>
                                    </p>
                                    <small class="text-muted">
                                      <?php if (!empty($search_query)): ?>
                                        Coba dengan kata kunci lain atau <a href="redemption-list.php">tampilkan semua</a>
                                      <?php else: ?>
                                        Belum ada permintaan penukaran kupon
                                      <?php endif; ?>
                                    </small>
                                  </div>
                                </td>
                              </tr>
                            <?php else: ?>
                              <?php foreach ($redemptions as $index => $redemption): ?>
                              <tr>
                                <td><strong>#<?= $redemption['id'] ?></strong></td>
                                <td>
                                  <small><?= date('d/m/Y', strtotime($redemption['redemption_date'])) ?></small>
                                  <br>
                                  <small class="text-muted"><?= date('H:i', strtotime($redemption['redemption_date'])) ?></small>
                                </td>
                                <td>
                                  <strong><?= htmlspecialchars($redemption['user_nama']) ?></strong>
                                  <br>
                                  <small class="text-muted">NIP: <?= $redemption['nip'] ?></small>
                                </td>
                                <td>
                                  <div class="d-flex align-items-center">
                                    <?php if ($redemption['gambar']): ?>
                                      <img src="../<?= $redemption['gambar'] ?>" alt="<?= htmlspecialchars($redemption['item_nama']) ?>" class="table-img me-2">
                                    <?php else: ?>
                                      <div class="table-img bg-light d-flex align-items-center justify-content-center me-2">
                                        <i class="bx bx-package text-muted"></i>
                                      </div>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($redemption['item_nama']) ?></span>
                                  </div>
                                </td>
                                <td>
                                  <span class="badge bg-primary rounded-pill"><?= $redemption['quantity'] ?></span>
                                </td>
                                <td>
                                  <span class="badge bg-warning text-dark">
                                    <i class="bx bx-coin me-1"></i><?= $redemption['kupon_used'] ?>
                                  </span>
                                </td>
                                <td>
                                  <?php if ($redemption['status'] == 'pending'): ?>
                                    <span class="badge bg-warning badge-status">
                                      <i class="bx bx-time me-1"></i>Menunggu
                                    </span>
                                  <?php elseif ($redemption['status'] == 'completed'): ?>
                                    <span class="badge bg-success badge-status">
                                      <i class="bx bx-check-circle me-1"></i>Selesai
                                    </span>
                                  <?php else: ?>
                                    <span class="badge bg-danger badge-status">
                                      <i class="bx bx-x-circle me-1"></i>Dibatalkan
                                    </span>
                                  <?php endif; ?>
                                </td>
                                <td>
                                  <div class="action-buttons d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $redemption['id'] ?>">
                                      <i class="bx bx-show"></i>
                                    </button>
                                    <?php if ($redemption['status'] == 'pending'): ?>
                                      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#statusModal<?= $redemption['id'] ?>">
                                        <i class="bx bx-check"></i>
                                      </button>
                                    <?php endif; ?>
                                  </div>
                                </td>
                              </tr>

                              <!-- Modal Detail -->
                              <div class="modal fade" id="detailModal<?= $redemption['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title">Detail Penukaran #<?= $redemption['id'] ?></h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                      <div class="row mb-3">
                                        <div class="col-md-6">
                                          <strong>Tanggal Penukaran:</strong>
                                          <p><?= date('d/m/Y H:i', strtotime($redemption['redemption_date'])) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                          <strong>Status:</strong>
                                          <p>
                                            <?php if ($redemption['status'] == 'pending'): ?>
                                              <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                            <?php elseif ($redemption['status'] == 'completed'): ?>
                                              <span class="badge bg-success">Selesai</span>
                                            <?php else: ?>
                                              <span class="badge bg-danger">Dibatalkan</span>
                                            <?php endif; ?>
                                          </p>
                                        </div>
                                      </div>
                                      
                                      <div class="row mb-3">
                                        <div class="col-md-6">
                                          <strong>User:</strong>
                                          <p><?= htmlspecialchars($redemption['user_nama']) ?> (NIP: <?= $redemption['nip'] ?>)</p>
                                        </div>
                                        <div class="col-md-6">
                                          <strong>Item:</strong>
                                          <div class="d-flex align-items-center">
                                            <?php if ($redemption['gambar']): ?>
                                              <img src="../<?= $redemption['gambar'] ?>" alt="<?= htmlspecialchars($redemption['item_nama']) ?>" class="table-img me-2">
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($redemption['item_nama']) ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      
                                      <div class="row mb-3">
                                        <div class="col-md-6">
                                          <strong>Quantity:</strong>
                                          <p><?= $redemption['quantity'] ?> item</p>
                                        </div>
                                        <div class="col-md-6">
                                          <strong>Total Kupon:</strong>
                                          <p><span class="badge bg-warning text-dark"><?= $redemption['kupon_used'] ?> Kupon</span></p>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                      <?php if ($redemption['status'] == 'pending'): ?>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal<?= $redemption['id'] ?>">
                                          Update Status
                                        </button>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <!-- Modal Update Status -->
                              <div class="modal fade" id="statusModal<?= $redemption['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <form method="POST">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Update Status Penukaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <input type="hidden" name="redemption_id" value="<?= $redemption['id'] ?>">
                                        
                                        <div class="mb-3">
                                          <label class="form-label">Status Saat Ini:</label>
                                          <p>
                                            <span class="badge bg-warning">
                                              <i class="bx bx-time me-1"></i>Menunggu
                                            </span>
                                          </p>
                                        </div>
                                        
                                        <div class="mb-3">
                                          <label for="status<?= $redemption['id'] ?>" class="form-label">Update Status:</label>
                                          <select class="form-select" id="status<?= $redemption['id'] ?>" name="status" required>
                                            <option value="completed">Selesai - Item sudah diberikan</option>
                                            <option value="cancelled">Dibatalkan - Penukaran dibatalkan</option>
                                          </select>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                          <i class="bx bx-info-circle me-2"></i>
                                          <strong>Perhatian:</strong> 
                                          <ul class="mb-0 mt-2">
                                            <li>Pastikan item sudah diberikan kepada user sebelum mengubah status menjadi "Selesai".</li>
                                            <li>Jika memilih "Dibatalkan", <?= $redemption['kupon_used'] ?> kupon akan dikembalikan ke user.</li>
                                          </ul>
                                        </div>
                                        
                                        <div id="refundInfo<?= $redemption['id'] ?>" class="refund-info" style="display: none;">
                                          <i class="bx bx-coin me-2"></i>
                                          <strong>Kupon akan dikembalikan:</strong> <?= $redemption['kupon_used'] ?> kupon akan dikembalikan ke saldo <?= htmlspecialchars($redemption['user_nama']) ?>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                      </div>
                                    </form>
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
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
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
      // Auto-hide alerts after 5 seconds
      document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
          const alerts = document.querySelectorAll('.alert');
          alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
          });
        }, 5000);
      });

      // Refresh page every 30 seconds to get latest data (only if there are pending items)
      setInterval(function() {
        const hasPending = document.querySelector('.badge.bg-warning');
        if (hasPending) {
          window.location.reload();
        }
      }, 30000);

      // Focus search input on page load if there's a search query
      document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && searchInput.value) {
          searchInput.focus();
          searchInput.select();
        }
      });

      // Show/hide refund info based on status selection
      document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'status') {
          const redemptionId = e.target.closest('form').querySelector('input[name="redemption_id"]').value;
          const refundInfo = document.getElementById('refundInfo' + redemptionId);
          
          if (e.target.value === 'cancelled') {
            refundInfo.style.display = 'block';
          } else {
            refundInfo.style.display = 'none';
          }
        }
      });
    </script>
  </body>
</html>