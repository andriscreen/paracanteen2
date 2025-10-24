<?php
require_once "../auth.php";
require_once "config/db.php";

if ($_SESSION['role'] !== 'user') {
    header("Location: ../form_login.php");
    exit;
}

// Get user's kupon balance
$user_id = $_SESSION['user_id'];
$query = "SELECT total_kupon FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$total_kupon_user = $user_data['total_kupon'] ?? 0;
?>
<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

    <!-- Custom CSS for redeem-kupon page -->
    <link rel="stylesheet" href="../assets/css/redeem-kupon.css" />
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>ParaCanteen</title>

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

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- animate orang -->
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
,        <?php include 'layout/sidebar.php'; ?>
        
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

              <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">User /</span> Penukaran Kupon
              </h4>

              <!-- Info kupon -->
              <div class="card mb-4">
                <div class="card-body">
                  <h5>Saldo Kupon Anda</h5>
                  <p class="fs-4 fw-bold <?= $total_kupon_user > 0 ? 'text-success' : 'text-warning' ?>">
                    💳 <?= $total_kupon_user ?> Kupon
                  </p>
                  <?php if ($total_kupon_user > 0): ?>
                    <small class="text-muted">Kupon diperoleh dari pemesanan makan mingguan.</small>
                  <?php else: ?>
                    <small class="text-warning">Anda belum memiliki kupon. Dapatkan kupon dengan memesan makanan mingguan.</small>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Daftar barang yang bisa ditukar -->
              <form id="redeemForm" method="POST" action="execution/process-redeem.php">
                
                <!-- HANYA SATU menu-container DI SINI -->
                <div class="menu-container">
                  <?php 
                    require_once 'config/redeem-items.php';
                  ?>

                  <?php foreach ($items as $item): ?>
                    <div class="menu-card">
                      <img src="<?= $item['gambar']; ?>" alt="<?= htmlspecialchars($item['nama']); ?>">
                      <div class="menu-card-body">
                        <h5><?= htmlspecialchars($item['nama']); ?></h5>
                        <p>Harga: <?= $item['kupon']; ?> Kupon</p>
                        <input 
                          type="number" 
                          name="qty[<?= $item['id']; ?>]" 
                          class="qty-input" 
                          placeholder="Qty" 
                          min="0" 
                          data-kupon="<?= $item['kupon']; ?>"
                          oninput="updateTotal()"
                        >
                        <input type="hidden" name="item_name[<?= $item['id']; ?>]" value="<?= $item['nama']; ?>">
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <!-- Total kupon -->
                <div class="total-box">
                  <h5>Total Kupon yang Akan Ditukarkan:</h5>
                  <p class="fs-5 fw-bold text-primary" id="totalKupon">0 Kupon</p>

                  <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary" id="btnTukar">Tukar Sekarang</button>
                  </div>
                </div>
              </form>
            </div>
            <!-- /Content -->
          </div>

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

    <!-- SweetAlert2 for nice popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        const totalKuponUser = <?= $total_kupon_user; ?>;
    </script>
    
    <!-- Custom JS for redeem-kupon page -->
    <script src="../assets/js/redeem-kupon.js"></script>
  </body>
</html>
