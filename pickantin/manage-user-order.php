<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'pickantin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; ?>
<?php
// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search filter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validasi sort column untuk mencegah SQL injection
$allowed_sorts = ['id', 'user_nama', 'nip', 'departemen', 'week_number', 'year_value', 'plant_name', 'place_name', 'nama_shift', 'created_at'];
if (!in_array($sort, $allowed_sorts)) {
    $sort = 'created_at';
}

// Validasi order
$order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

// Query untuk mendapatkan data orders dengan join
$query = "SELECT o.*, u.nama as user_nama, u.nip, u.departemen, w.week_number, y.year_value, p.name as plant_name, pl.name as place_name, s.nama_shift
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          LEFT JOIN week w ON o.week_id = w.id 
          LEFT JOIN year y ON o.year_id = y.id 
          LEFT JOIN plant p ON o.plant_id = p.id 
          LEFT JOIN place pl ON o.place_id = pl.id 
          LEFT JOIN shift s ON o.shift_id = s.id 
          WHERE 1=1";

$countQuery = "SELECT COUNT(*) as total FROM orders o WHERE 1=1";

if (!empty($search)) {
    $searchTerm = "%$search%";
    $query .= " AND (u.nama LIKE ? OR u.nip LIKE ? OR p.name LIKE ? OR u.departemen LIKE ?)";
    $countQuery .= " AND EXISTS (SELECT 1 FROM users u WHERE o.user_id = u.id AND (u.nama LIKE ? OR u.nip LIKE ? OR u.departemen LIKE ?))";
}

$query .= " ORDER BY $sort $order LIMIT ? OFFSET ?";

// Eksekusi query count
if (!empty($search)) {
    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
} else {
    $stmt = $conn->prepare($countQuery);
}
$stmt->execute();
$countResult = $stmt->get_result();
$totalOrders = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalOrders / $limit);

// Eksekusi query main
$stmt = $conn->prepare($query);
if (!empty($search)) {
    $stmt->bind_param("ssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$orders = $stmt->get_result();

// Handle delete action
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    
    // Mulai transaction
    $conn->begin_transaction();
    
    try {
        // Hapus dari order_menus terlebih dahulu
        $deleteMenus = $conn->prepare("DELETE FROM order_menus WHERE order_id = ?");
        $deleteMenus->bind_param("i", $order_id);
        $deleteMenus->execute();
        
        // Hapus dari kupon_history jika ada
        $deleteKupon = $conn->prepare("DELETE FROM kupon_history WHERE order_id = ?");
        $deleteKupon->bind_param("i", $order_id);
        $deleteKupon->execute();
        
        // Hapus order
        $deleteOrder = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $deleteOrder->bind_param("i", $order_id);
        $deleteOrder->execute();
        
        $conn->commit();
        $_SESSION['success'] = "Order berhasil dihapus!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Gagal menghapus order: " . $e->getMessage();
    }
    
    header("Location: manage-user-order.php");
    exit;
}

// Fungsi untuk menentukan icon sort
function getSortIcon($currentSort, $column, $currentOrder) {
    if ($currentSort === $column) {
        return $currentOrder === 'ASC' ? '↑' : '↓';
    }
    return '';
}

// Fungsi untuk mendapatkan order berikutnya
function getNextOrder($currentSort, $column, $currentOrder) {
    if ($currentSort === $column) {
        return $currentOrder === 'ASC' ? 'DESC' : 'ASC';
    }
    return 'ASC';
}
?>

<!DOCTYPE html>
<html
  lang="id"
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

    <title>Kelola Orders - ParaCanteen</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
    <style>
      .table-responsive { 
        max-height: 600px; 
        border-radius: 8px;
      }
      .action-buttons { 
        white-space: nowrap; 
      }
      .search-form { 
        max-width: 400px; 
      }
      .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
      }
      .badge-count {
        font-size: 0.9em;
        background: rgba(255,255,255,0.2);
      }
      .badge-departemen {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
      }
      .sortable {
        cursor: pointer;
        user-select: none;
      }
      .sortable:hover {
        background-color: rgba(0,0,0,0.05);
      }
      .sort-icon {
        margin-left: 5px;
        font-weight: bold;
      }
      .current-sort {
        background-color: rgba(102, 126, 234, 0.1);
        font-weight: bold;
      }
    </style>

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
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
                <div class="col-lg-12 mb-4 order-0">
                  <div class="card">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-12">
                        <div class="card-body">
                          <h4 class="card-title text-primary">
                            <i class="bx bx-list-ul"></i> Kelola Orders User
                          </h4>
                          
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

                          <!-- Search Form -->
                          <div class="card mb-4">
                            <div class="card-body">
                              <form method="GET" class="row g-3">
                                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                                <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
                                <div class="col-md-8">
                                  <div class="input-group">
                                    <span class="input-group-text">
                                      <i class="bx bx-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama user, NIP, plant, atau departemen..." value="<?= htmlspecialchars($search) ?>">
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <button class="btn btn-primary" type="submit">
                                    <i class="bx bx-search"></i> Cari
                                  </button>
                                </div>
                                <div class="col-md-2">
                                  <a href="manage-user-order.php" class="btn btn-outline-secondary">
                                    <i class="bx bx-refresh"></i> Reset
                                  </a>
                                </div>
                              </form>
                            </div>
                          </div>

                          <!-- Orders Table -->
                          <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                              <h5 class="mb-0">
                                <i class="bx bx-list-ol"></i> Daftar Orders
                                <span class="badge badge-count"><?= $totalOrders ?> total</span>
                              </h5>
                              <div class="text-muted small">
                                Diurutkan berdasarkan: 
                                <strong>
                                  <?php 
                                  $sortLabels = [
                                    'id' => 'ID',
                                    'user_nama' => 'User',
                                    'nip' => 'NIP',
                                    'departemen' => 'Departemen',
                                    'week_number' => 'Minggu',
                                    'year_value' => 'Tahun',
                                    'plant_name' => 'Plant',
                                    'place_name' => 'Place',
                                    'nama_shift' => 'Shift',
                                    'created_at' => 'Tanggal'
                                  ];
                                  echo $sortLabels[$sort] . ' (' . $order . ')';
                                  ?>
                                </strong>
                              </div>
                            </div>
                            <div class="card-body">
                              <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                  <thead class="table-light">
                                    <tr>
                                      <th class="<?= $sort === 'id' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=id&order=<?= getNextOrder($sort, 'id', $order) ?>" class="text-dark text-decoration-none sortable">
                                          ID <?= getSortIcon($sort, 'id', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'user_nama' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=user_nama&order=<?= getNextOrder($sort, 'user_nama', $order) ?>" class="text-dark text-decoration-none sortable">
                                          User <?= getSortIcon($sort, 'user_nama', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'nip' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=nip&order=<?= getNextOrder($sort, 'nip', $order) ?>" class="text-dark text-decoration-none sortable">
                                          NIP <?= getSortIcon($sort, 'nip', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'departemen' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=departemen&order=<?= getNextOrder($sort, 'departemen', $order) ?>" class="text-dark text-decoration-none sortable">
                                          Departemen <?= getSortIcon($sort, 'departemen', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'week_number' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=week_number&order=<?= getNextOrder($sort, 'week_number', $order) ?>" class="text-dark text-decoration-none sortable">
                                          Minggu <?= getSortIcon($sort, 'week_number', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'year_value' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=year_value&order=<?= getNextOrder($sort, 'year_value', $order) ?>" class="text-dark text-decoration-none sortable">
                                          Tahun <?= getSortIcon($sort, 'year_value', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'plant_name' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=plant_name&order=<?= getNextOrder($sort, 'plant_name', $order) ?>" class="text-dark text-decoration-none sortable">
                                          Plant <?= getSortIcon($sort, 'plant_name', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'place_name' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=place_name&order=<?= getNextOrder($sort, 'place_name', $order) ?>" class="text-dark text-decoration-none sortable">
                                          Place <?= getSortIcon($sort, 'place_name', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'nama_shift' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=nama_shift&order=<?= getNextOrder($sort, 'nama_shift', $order) ?>" class="text-dark text-decoration-none sortable">
                                          Shift <?= getSortIcon($sort, 'nama_shift', $order) ?>
                                        </a>
                                      </th>
                                      <th class="<?= $sort === 'created_at' ? 'current-sort' : '' ?>">
                                        <a href="?page=<?= $page ?>&search=<?= urlencode($search) ?>&sort=created_at&order=<?= getNextOrder($sort, 'created_at', $order) ?>" class="text-dark text-decoration-none sortable">
                                          Tanggal <?= getSortIcon($sort, 'created_at', $order) ?>
                                        </a>
                                      </th>
                                      <th>Aksi</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php while ($order_data = $orders->fetch_assoc()): ?>
                                    <tr>
                                      <td><strong>#<?= $order_data['id'] ?></strong></td>
                                      <td><?= htmlspecialchars($order_data['user_nama']) ?></td>
                                      <td><?= htmlspecialchars($order_data['nip']) ?></td>
                                      <td>
                                        <?php if (!empty($order_data['departemen'])): ?>
                                          <span class="badge badge-departemen"><?= htmlspecialchars($order_data['departemen']) ?></span>
                                        <?php else: ?>
                                          <span class="text-muted">-</span>
                                        <?php endif; ?>
                                      </td>
                                      <td>Minggu <?= $order_data['week_number'] ?></td>
                                      <td><?= $order_data['year_value'] ?></td>
                                      <td><?= htmlspecialchars($order_data['plant_name']) ?></td>
                                      <td><?= htmlspecialchars($order_data['place_name']) ?></td>
                                      <td>
                                        <span class="badge bg-label-primary"><?= $order_data['nama_shift'] ?? '-' ?></span>
                                      </td>
                                      <td><?= date('d/m/Y H:i', strtotime($order_data['created_at'])) ?></td>
                                      <td class="action-buttons">
                                        <a href="execution/edit_order.php?id=<?= $order_data['id'] ?>" class="btn btn-sm btn-warning">
                                          <i class="bx bx-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $order_data['id'] ?>">
                                          <i class="bx bx-trash"></i> Hapus
                                        </button>
                                      </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?= $order_data['id'] ?>" tabindex="-1">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                          </div>
                                          <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus order dari <strong><?= htmlspecialchars($order_data['user_nama']) ?></strong>?</p>
                                            <p><strong>Departemen:</strong> <?= htmlspecialchars($order_data['departemen']) ?></p>
                                            <p><strong>Minggu:</strong> <?= $order_data['week_number'] ?>, <strong>Plant:</strong> <?= htmlspecialchars($order_data['plant_name']) ?></p>
                                            <p class="text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                                          </div>
                                          <div class="modal-footer">
                                            <form method="POST">
                                              <input type="hidden" name="order_id" value="<?= $order_data['id'] ?>">
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                              <button type="submit" name="delete_order" class="btn btn-danger">Hapus</button>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <?php endwhile; ?>
                                    
                                    <?php if ($orders->num_rows === 0): ?>
                                    <tr>
                                      <td colspan="11" class="text-center text-muted py-4">
                                        <i class="bx bx-inbox bx-lg mb-2"></i><br>
                                        Tidak ada data orders ditemukan
                                      </td>
                                    </tr>
                                    <?php endif; ?>
                                  </tbody>
                                </table>
                              </div>

                              <!-- Pagination -->
                              <?php if ($totalPages > 1): ?>
                              <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                  <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>">
                                      <i class="bx bx-chevron-left"></i> Previous
                                    </a>
                                  </li>
                                  
                                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                      <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i ?></a>
                                    </li>
                                  <?php endfor; ?>
                                  
                                  <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>">
                                      Next <i class="bx bx-chevron-right"></i>
                                    </a>
                                  </li>
                                </ul>
                              </nav>
                              <?php endif; ?>
                            </div>
                          </div>
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
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>