<?php include "../auth.php"; ?>
<?php 
if ($_SESSION['role'] !== 'user') { 
    header("Location: ../form_login.php"); 
    exit; 
} 

// Koneksi ke database
include 'config/db.php';

// Ambil week_id dari filter
$selected_week = isset($_GET['week_id']) ? (int)$_GET['week_id'] : 0;

// Ambil daftar week untuk dropdown filter
$weeks_result = $conn->query("SELECT DISTINCT week_id 
                             FROM menu 
                             ORDER BY week_id ASC");

// Query daftar menu + gambar
if ($selected_week > 0) {
    $sql = "SELECT m.*, mi.image_url  
            FROM menu m 
            LEFT JOIN menu_images mi ON m.week_id = mi.week_id AND m.day = mi.day 
            WHERE m.week_id = $selected_week
            ORDER BY FIELD(m.day, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') ASC";
} else {
    $sql = "SELECT m.*, mi.image_url 
            FROM menu m 
            LEFT JOIN menu_images mi ON m.week_id = mi.week_id AND m.day = mi.day
            ORDER BY m.week_id ASC, 
                     FIELD(m.day, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') ASC";
}

$result = $conn->query($sql);
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
    <title>Daftar Menu | ParaCanteen</title>

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
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

    <style>
      .menu-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 20px;
      }
      .menu-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: 0.3s;
        cursor: pointer;
      }
      .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      }
      .menu-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
      }
      .menu-card-body {
        padding: 10px;
      }
      .menu-card-body h5 {
        font-size: 16px;
        margin-bottom: 5px;
      }
      .menu-card-body p {
        font-size: 14px;
        margin: 0;
        color: #666;
      }
      .filter-form {
        max-width: 250px;
        margin-bottom: 20px;
      }
      .detail-btn {
        margin-top: 8px;
        padding: 5px 12px;
        background: #696cff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        transition: background 0.3s;
      }
      .detail-btn:hover {
        background: #5a5fe3;
      }
      
      /* Modal Styles */
      .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
      }
      .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      }
      .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
      }
      .close:hover {
        color: #000;
      }
      .modal-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
      }
      .modal-body {
        padding: 10px 0;
      }
      .keterangan-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-top: 10px;
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
            <div class="container mt-4">
              <div class="card shadow-sm p-4">
                <h4 class="mb-4"><i class="bx bx-food-menu"></i> Daftar Menu</h4>

                <!-- Filter Week -->
                <form class="filter-form" method="GET" action="">
                  <label for="week_id" class="form-label">Filter by Week:</label>
                  <select class="form-select" name="week_id" id="week_id" onchange="this.form.submit()">
                    <option value="0">All Weeks</option>
                    <?php while($week = $weeks_result->fetch_assoc()): ?>
                      <option value="<?= $week['week_id']; ?>" <?= ($week['week_id']==$selected_week) ? 'selected' : ''; ?>>
                        Week <?= $week['week_id']; ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </form>

                <!-- Menu List -->
                <div class="menu-container">
                  <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                      <div class="menu-card" onclick="showMenuDetail(<?= htmlspecialchars(json_encode($row)); ?>)">
                        <img src="<?= $row['image_url'] ? '../' . $row['image_url'] : '../assets/img/menu/no-image.jpg'; ?>" 
                          alt="<?= htmlspecialchars($row['menu_name']); ?>">
                        <div class="menu-card-body">
                          <h5><?= htmlspecialchars($row['menu_name']); ?></h5>
                          <p>Day: <?= $row['day']; ?></p>
                          <p>Week <?= $row['week_id']; ?></p>
                          <button class="detail-btn" onclick="event.stopPropagation(); showMenuDetail(<?= htmlspecialchars(json_encode($row)); ?>)">
                            <i class="bx bx-info-circle"></i> Detail
                          </button>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <p class="text-muted">Tidak ada menu untuk week yang dipilih.</p>
                  <?php endif; ?>
                </div>

              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  © <script>document.write(new Date().getFullYear());</script>,
                  Part of <a href="#" class="footer-link fw-bolder">ParagonCorp</a>
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

    <!-- Modal untuk Detail Menu -->
    <div id="menuModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <span class="close" onclick="closeModal()">&times;</span>
          <h4 id="modalTitle">Detail Menu</h4>
        </div>
        <div class="modal-body">
          <div id="modalContent">
            <!-- Konten detail akan dimuat di sini -->
          </div>
        </div>
      </div>
    </div>

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

    <script>
      // Fungsi untuk menampilkan detail menu
      function showMenuDetail(menuData) {
        const modal = document.getElementById('menuModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        // Set judul modal
        modalTitle.textContent = menuData.menu_name;
        
        // Buat konten detail
        let content = `
          <div class="row">
            <div class="col-md-4">
              <img src="${menuData.image_url ? '../' + menuData.image_url : '../assets/img/menu/no-image.jpg'}" 
                   alt="${menuData.menu_name}" 
                   style="width: 100%; height: 150px; object-fit: cover; border-radius: 5px;">
            </div>
            <div class="col-md-8">
              <p><strong>Hari:</strong> ${menuData.day}</p>
              <p><strong>Week:</strong> ${menuData.week_id}</p>
            </div>
          </div>
        `;
        
        // Tambahkan keterangan jika ada
        if (menuData.keterangan && menuData.keterangan.trim() !== '') {
          content += `
            <div class="keterangan-content">
              <h6><strong>Keterangan Menu:</strong></h6>
              <p>${menuData.keterangan}</p>
            </div>
          `;
        } else {
          content += `
            <div class="keterangan-content">
              <p class="text-muted"><em>Tidak ada keterangan tambahan untuk menu ini.</em></p>
            </div>
          `;
        }
        
        modalContent.innerHTML = content;
        modal.style.display = 'block';
      }
      
      // Fungsi untuk menutup modal
      function closeModal() {
        document.getElementById('menuModal').style.display = 'none';
      }
      
      // Tutup modal ketika klik di luar konten modal
      window.onclick = function(event) {
        const modal = document.getElementById('menuModal');
        if (event.target == modal) {
          closeModal();
        }
      }
      
      // Tutup modal dengan tombol ESC
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          closeModal();
        }
      });
    </script>
  </body>
</html>

<?php $conn->close(); ?>