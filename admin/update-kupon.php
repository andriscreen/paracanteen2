<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { 
    header("Location: ../form_login.php"); 
    exit; 
} 
include 'config/db.php'; 

// Inisialisasi variabel
$success = '';
$error = '';
$items = [];

// Tambah item baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_item'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $kupon = (int)$_POST['kupon'];
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $aktif = isset($_POST['aktif']) ? 1 : 0;
    
    // Handle upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/barang/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $filename;
        
        // Validasi file gambar
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if ($_FILES['gambar']['size'] <= $max_file_size) {
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    // PERBAIKAN: Simpan path tanpa ../ di depan
                    $gambar = 'assets/img/barang/' . $filename;
                } else {
                    $error = "Gagal mengupload gambar!";
                }
            } else {
                $error = "Ukuran file terlalu besar! Maksimal 2MB.";
            }
        } else {
            $error = "Format file tidak didukung! Hanya JPG, PNG, GIF yang diperbolehkan.";
        }
    }
    
    // Hanya eksekusi query jika tidak ada error
    if (empty($error)) {
        $sql = "INSERT INTO redeem_items (nama, kupon, gambar, keterangan, aktif) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissi", $nama, $kupon, $gambar, $keterangan, $aktif);
        
        if ($stmt->execute()) {
            $success = "Item berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan item: " . $conn->error;
        }
        $stmt->close();
    }
}

// Update item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_item'])) {
    $id = (int)$_POST['id'];
    $nama = $conn->real_escape_string($_POST['nama']);
    $kupon = (int)$_POST['kupon'];
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $aktif = isset($_POST['aktif']) ? 1 : 0;
    
    // Handle upload gambar baru
    $gambar = $_POST['gambar_lama'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/barang/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $filename;
        
        // Validasi file gambar
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if ($_FILES['gambar']['size'] <= $max_file_size) {
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    // Hapus gambar lama jika ada
                    if ($gambar && file_exists("../" . $gambar)) {
                        unlink("../" . $gambar);
                    }
                    // PERBAIKAN: Simpan path tanpa ../ di depan
                    $gambar = 'assets/img/barang/' . $filename;
                } else {
                    $error = "Gagal mengupload gambar!";
                }
            } else {
                $error = "Ukuran file terlalu besar! Maksimal 2MB.";
            }
        } else {
            $error = "Format file tidak didukung! Hanya JPG, PNG, GIF yang diperbolehkan.";
        }
    }
    
    // Hanya eksekusi query jika tidak ada error
    if (empty($error)) {
        $sql = "UPDATE redeem_items SET nama = ?, kupon = ?, gambar = ?, keterangan = ?, aktif = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissii", $nama, $kupon, $gambar, $keterangan, $aktif, $id);
        
        if ($stmt->execute()) {
            $success = "Item berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate item: " . $conn->error;
        }
        $stmt->close();
    }
}

// Hapus item
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Ambil path gambar sebelum hapus
    $sql = "SELECT gambar FROM redeem_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    
    // Hapus dari database
    $sql = "DELETE FROM redeem_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Hapus file gambar jika ada
        if ($item && $item['gambar'] && file_exists("../" . $item['gambar'])) {
            unlink("../" . $item['gambar']);
        }
        $success = "Item berhasil dihapus!";
    } else {
        $error = "Gagal menghapus item: " . $conn->error;
    }
    $stmt->close();
}

// Ambil semua item
$sql = "SELECT * FROM redeem_items ORDER BY id DESC";
$result = $conn->query($sql);

if ($result) {
    $items = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $error = "Gagal mengambil data: " . $conn->error;
}
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

    <title>Kelola Item Penukaran Kupon - ParaCanteen</title>
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
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e3e6f0;
      }
      .badge-kupon {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
      }
      .action-buttons .btn {
        padding: 0.375rem 0.75rem;
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
                        <i class="bx bx-gift me-2"></i>Kelola Item Penukaran Kupon
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

              <!-- Form Tambah Item -->
              <div class="row">
                <div class="col-12">
                  <div class="card mb-4">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-plus-circle me-2"></i>Tambah Item Baru
                      </h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                          <div class="col-md-4 mb-3">
                            <label for="nama" class="form-label">Nama Item <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama item" required>
                          </div>
                          <div class="col-md-3 mb-3">
                            <label for="kupon" class="form-label">Jumlah Kupon <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="kupon" name="kupon" min="1" placeholder="0" required>
                          </div>
                          <div class="col-md-5 mb-3">
                            <label for="gambar" class="form-label">Gambar Item</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                          </div>
                        </div>
                        <div class="mb-3">
                          <label for="keterangan" class="form-label">Keterangan</label>
                          <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Deskripsi singkat tentang item..."></textarea>
                        </div>
                        <div class="mb-3">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="aktif" name="aktif" checked>
                            <label class="form-check-label" for="aktif">Aktifkan item</label>
                          </div>
                        </div>
                        <button type="submit" name="tambah_item" class="btn btn-primary">
                          <i class="bx bx-save me-2"></i>Simpan Item
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Daftar Item -->
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2"></i>Daftar Item Penukaran
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-striped table-hover">
                          <thead>
                            <tr>
                              <th width="5%">#</th>
                              <th width="15%">Gambar</th>
                              <th width="20%">Nama Item</th>
                              <th width="15%">Kupon</th>
                              <th width="25%">Keterangan</th>
                              <th width="10%">Status</th>
                              <th width="10%">Aksi</th>
                            </tr>
                          </thead>
                          <tbody class="table-border-bottom-0">
                            <?php if (empty($items)): ?>
                              <tr>
                                <td colspan="7" class="text-center py-4">
                                  <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-package text-muted mb-2" style="font-size: 3rem;"></i>
                                    <p class="text-muted mb-0">Tidak ada data item</p>
                                    <small class="text-muted">Silakan tambah item baru menggunakan form di atas</small>
                                  </div>
                                </td>
                              </tr>
                            <?php else: ?>
                              <?php foreach ($items as $index => $item): ?>
                              <tr>
                                <td><strong><?= $index + 1 ?></strong></td>
                                <td>
                                  <?php if ($item['gambar']): ?>
                                    <img src="../<?= $item['gambar'] ?>" alt="<?= $item['nama'] ?>" class="table-img">
                                  <?php else: ?>
                                    <div class="table-img bg-light d-flex align-items-center justify-content-center">
                                      <i class="bx bx-image text-muted" style="font-size: 1.5rem;"></i>
                                    </div>
                                  <?php endif; ?>
                                </td>
                                <td>
                                  <strong><?= htmlspecialchars($item['nama']) ?></strong>
                                </td>
                                <td>
                                  <span class="badge bg-warning badge-kupon">
                                    <i class="bx bx-coin me-1"></i><?= $item['kupon'] ?> Kupon
                                  </span>
                                </td>
                                <td>
                                  <small class="text-muted"><?= htmlspecialchars($item['keterangan'] ?? '-') ?></small>
                                </td>
                                <td>
                                  <span class="badge <?= $item['aktif'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $item['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                  </span>
                                </td>
                                <td>
                                  <div class="action-buttons d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id'] ?>">
                                      <i class="bx bx-edit"></i>
                                    </button>
                                    <a href="?hapus=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus item ini?')">
                                      <i class="bx bx-trash"></i>
                                    </a>
                                  </div>
                                </td>
                              </tr>

                              <!-- Modal Edit -->
                              <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                  <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Edit Item - <?= htmlspecialchars($item['nama']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="gambar_lama" value="<?= $item['gambar'] ?>">
                                        
                                        <div class="row">
                                          <div class="col-md-6 mb-3">
                                            <label for="nama_edit<?= $item['id'] ?>" class="form-label">Nama Item <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_edit<?= $item['id'] ?>" name="nama" value="<?= htmlspecialchars($item['nama']) ?>" required>
                                          </div>
                                          
                                          <div class="col-md-6 mb-3">
                                            <label for="kupon_edit<?= $item['id'] ?>" class="form-label">Jumlah Kupon <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="kupon_edit<?= $item['id'] ?>" name="kupon" value="<?= $item['kupon'] ?>" min="1" required>
                                          </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                          <label for="gambar_edit<?= $item['id'] ?>" class="form-label">Gambar Item</label>
                                          <?php if ($item['gambar']): ?>
                                            <div class="mb-2">
                                              <p class="text-muted mb-1">Gambar saat ini:</p>
                                              <img src="../<?= $item['gambar'] ?>" alt="Current image" class="table-img">
                                            </div>
                                          <?php endif; ?>
                                          <input type="file" class="form-control" id="gambar_edit<?= $item['id'] ?>" name="gambar" accept="image/*">
                                          <div class="form-text">Kosongkan jika tidak ingin mengubah gambar</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                          <label for="keterangan_edit<?= $item['id'] ?>" class="form-label">Keterangan</label>
                                          <textarea class="form-control" id="keterangan_edit<?= $item['id'] ?>" name="keterangan" rows="3"><?= htmlspecialchars($item['keterangan'] ?? '') ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                          <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="aktif_edit<?= $item['id'] ?>" name="aktif" <?= $item['aktif'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="aktif_edit<?= $item['id'] ?>">Aktifkan item</label>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="update_item" class="btn btn-primary">Update Item</button>
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
    </script>
  </body>
</html>