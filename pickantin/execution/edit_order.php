<?php
include "../config/db.php";
include "../../auth.php";

if ($_SESSION['role'] !== 'pickantin') {
    header("Location: ../../form_login.php");
    exit;
}

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    $_SESSION['error'] = "Order ID tidak valid!";
    header('Location: ../manage-user-order.php');
    exit;
}

// Get order data
$orderQuery = $conn->prepare("SELECT o.*, u.nama as user_nama, u.nip FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$orderQuery->bind_param("i", $order_id);
$orderQuery->execute();
$order = $orderQuery->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order tidak ditemukan!";
    header('Location: ../manage-user-order.php');
    exit;
}

// Get order menus
$menusQuery = $conn->prepare("
    SELECT om.*, m.menu_name, m.day 
    FROM order_menus om 
    LEFT JOIN menu m ON om.menu_id = m.id 
    WHERE om.order_id = ?
");
$menusQuery->bind_param("i", $order_id);
$menusQuery->execute();
$orderMenus = $menusQuery->get_result();

// Get available weeks, plants, places, shifts
$weeks = $conn->query("SELECT * FROM week ORDER BY week_number");
$plants = $conn->query("SELECT * FROM plant");
$places = $conn->query("SELECT * FROM place");
$shifts = $conn->query("SELECT * FROM shift");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $week_id = $_POST['week_id'];
    $year_id = $_POST['year_id'];
    $plant_id = $_POST['plant_id'];
    $place_id = $_POST['place_id'];
    $shift_id = $_POST['shift_id'];
    
    // Update order
    $updateOrder = $conn->prepare("UPDATE orders SET week_id = ?, year_id = ?, plant_id = ?, place_id = ?, shift_id = ? WHERE id = ?");
    $updateOrder->bind_param("iiiiii", $week_id, $year_id, $plant_id, $place_id, $shift_id, $order_id);
    
    if ($updateOrder->execute()) {
        $_SESSION['success'] = "Order berhasil diupdate!";
        header("Location: ../manage-user-order.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate order!";
    }
}

// Handle menu update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_menus'])) {
    $conn->begin_transaction();
    
    try {
        // Delete existing menu selections
        $deleteMenus = $conn->prepare("DELETE FROM order_menus WHERE order_id = ?");
        $deleteMenus->bind_param("i", $order_id);
        $deleteMenus->execute();
        
        // Insert new menu selections
        if (isset($_POST['menus'])) {
            $insertMenu = $conn->prepare("INSERT INTO order_menus (order_id, menu_id, makan, kupon, libur) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($_POST['menus'] as $menu_id => $option) {
                $selectedOption = $option['option'] ?? '';
                
                $makan = ($selectedOption === 'makan') ? 1 : 0;
                $kupon = ($selectedOption === 'kupon') ? 1 : 0;
                $libur = ($selectedOption === 'libur') ? 1 : 0;
                
                $insertMenu->bind_param("iiiii", $order_id, $menu_id, $makan, $kupon, $libur);
                $insertMenu->execute();
            }
        }
        
        $conn->commit();
        $_SESSION['success'] = "Menu berhasil diupdate!";
        header("Location: edit_order.php?id=$order_id");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Gagal mengupdate menu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html
  lang="id"
  dir="ltr"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Edit Order - ParaCanteen</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Page CSS -->
    <style>
      body {
        background-color: #f5f5f5;
        font-family: 'Public Sans', sans-serif;
      }
      .table-responsive { 
        border-radius: 8px;
      }
      .card-header {
        background: linear-gradient(135deg, #8fa3ffff 0%, #8fa3ffff 100%);
        color: white;
      }
      .form-check-input {
        transform: scale(1.2);
      }
      .badge-order {
        background: rgba(255,255,255,0.2);
        font-size: 0.9em;
      }
      .main-container {
        min-height: 100vh;
        padding: 20px;
      }
      .navbar-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      }
    </style>
  </head>

  <body>
    <!-- Simple Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">
          <i class="bx bx-food-menu"></i> ParaCanteen - Edit Order
        </a>
        <div class="d-flex">
          <a href="../manage-user-order.php" class="btn btn-light btn-sm">
            <i class="bx bx-arrow-back"></i> Kembali ke Manage Orders
          </a>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-12 col-xl-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="text-primary">
                <i class="bx bx-edit"></i> Edit Order 
                <span class="badge bg-primary">#<?= $order_id ?></span>
              </h4>
            </div>

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

            <!-- Order Information -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0 text-white">
                  <i class="bx bx-info-circle"></i> Informasi Order
                </h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <p><strong>User:</strong> <?= htmlspecialchars($order['user_nama']) ?></p>
                    <p><strong>NIP:</strong> <?= htmlspecialchars($order['nip']) ?></p>
                  </div>
                  <div class="col-md-4">
                    <p><strong>Tanggal Order:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>Status:</strong> 
                      <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : 'primary' ?>">
                        <?= ucfirst($order['status']) ?>
                      </span>
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Edit Order Form -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0 text-white">
                  <i class="bx bx-cog"></i> Edit Detail Order
                </h5>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="week_id" class="form-label">Minggu</label>
                      <select class="form-select" id="week_id" name="week_id" required>
                        <option value="">Pilih Minggu</option>
                        <?php while ($week = $weeks->fetch_assoc()): ?>
                          <option value="<?= $week['id'] ?>" <?= $week['id'] == $order['week_id'] ? 'selected' : '' ?>>
                            Minggu <?= $week['week_number'] ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    
                    <div class="col-md-6">
                      <label for="year_id" class="form-label">Tahun</label>
                      <select class="form-select" id="year_id" name="year_id" required>
                        <option value="1" <?= $order['year_id'] == 1 ? 'selected' : '' ?>>2025</option>
                        <option value="2" <?= $order['year_id'] == 2 ? 'selected' : '' ?>>2026</option>
                        <option value="3" <?= $order['year_id'] == 3 ? 'selected' : '' ?>>2027</option>
                      </select>
                    </div>
                    
                    <div class="col-md-6">
                      <label for="plant_id" class="form-label">Plant</label>
                      <select class="form-select" id="plant_id" name="plant_id" required>
                        <option value="">Pilih Plant</option>
                        <?php while ($plant = $plants->fetch_assoc()): ?>
                          <option value="<?= $plant['id'] ?>" <?= $plant['id'] == $order['plant_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($plant['name']) ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    
                    <div class="col-md-6">
                      <label for="place_id" class="form-label">Place</label>
                      <select class="form-select" id="place_id" name="place_id" required>
                        <option value="">Pilih Place</option>
                        <?php while ($place = $places->fetch_assoc()): ?>
                          <option value="<?= $place['id'] ?>" <?= $place['id'] == $order['place_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($place['name']) ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    
                    <div class="col-md-6">
                      <label for="shift_id" class="form-label">Shift</label>
                      <select class="form-select" id="shift_id" name="shift_id">
                        <option value="">Pilih Shift</option>
                        <?php while ($shift = $shifts->fetch_assoc()): ?>
                          <option value="<?= $shift['id'] ?>" <?= $shift['id'] == $order['shift_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($shift['nama_shift']) ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    
                    <div class="col-12">
                      <button type="submit" name="update_order" class="btn btn-primary">
                        <i class="bx bx-save"></i> Update Order
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Edit Menus Form -->
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0 text-white">
                  <i class="bx bx-food-menu"></i> Edit Menu Selection
                </h5>
              </div>
              <div class="card-body">
                <form method="POST">
                  <?php
                  // Get menus for the selected week
                  $weekMenus = $conn->prepare("SELECT * FROM menu WHERE week_id = ? ORDER BY FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')");
                  $weekMenus->bind_param("i", $order['week_id']);
                  $weekMenus->execute();
                  $menus = $weekMenus->get_result();
                  
                  // Create array of current menu selections
                  $currentSelections = [];
                  while ($menu = $orderMenus->fetch_assoc()) {
                    $currentSelections[$menu['menu_id']] = [
                      'makan' => $menu['makan'],
                      'kupon' => $menu['kupon'],
                      'libur' => $menu['libur']
                    ];
                  }
                  ?>
                  
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead class="table-light">
                        <tr>
                          <th>Hari</th>
                          <th>Menu</th>
                          <th class="text-center">Makan</th>
                          <th class="text-center">Kupon</th>
                          <th class="text-center">Libur</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php while ($menu = $menus->fetch_assoc()): 
                              $current = $currentSelections[$menu['id']] ?? ['makan' => 0, 'kupon' => 0, 'libur' => 0];
                              
                              // Tentukan nilai yang terpilih
                              $selectedOption = '';
                              if ($current['makan']) $selectedOption = 'makan';
                              if ($current['kupon']) $selectedOption = 'kupon';
                              if ($current['libur']) $selectedOption = 'libur';
                          ?>
                          <tr>
                              <td>
                                  <span class="badge bg-primary"><?= $menu['day'] ?></span>
                              </td>
                              <td><?= htmlspecialchars($menu['menu_name']) ?></td>
                              <td class="text-center">
                                  <input type="radio" name="menus[<?= $menu['id'] ?>][option]" value="makan" 
                                      <?= $selectedOption == 'makan' ? 'checked' : '' ?> class="form-check-input">
                              </td>
                              <td class="text-center">
                                  <input type="radio" name="menus[<?= $menu['id'] ?>][option]" value="kupon" 
                                      <?= $selectedOption == 'kupon' ? 'checked' : '' ?> class="form-check-input">
                              </td>
                              <td class="text-center">
                                  <input type="radio" name="menus[<?= $menu['id'] ?>][option]" value="libur" 
                                      <?= $selectedOption == 'libur' ? 'checked' : '' ?> class="form-check-input">
                              </td>
                          </tr>
                          <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                  
                  <div class="mt-3">
                    <button type="submit" name="update_menus" class="btn btn-success">
                      <i class="bx bx-save"></i> Update Menu Selection
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-3">
      <div class="container-fluid">
        <div class="text-center text-muted">
          © <?= date('Y') ?>, Part of <a href="#" class="text-decoration-none fw-bolder">ParagonCorp</a>
        </div>
      </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Page JS -->
    <script>
      // Auto refresh menus when week changes
      document.getElementById('week_id').addEventListener('change', function() {
        // You can add AJAX functionality here to load menus based on selected week
        console.log('Week changed to: ' + this.value);
      });
    </script>
  </body>
</html>