<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'user') { header("Location: ../form_login.php"); exit; } ?>
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
              <div class="row g-4">
                <div class="col-12">
                  <div class="card mb-4">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h2 class="card-title text-primary fw-bold">Hallo Paragonian! 🎉</h2>
                          <p class="mb-4">
                            "Tubuh kita adalah amanah dari <strong>Allah.SWT</strong>. Maka, makanlah yang baik dan bergizi agar tubuh kita tetap kuat untuk beribadah, bekerja, dan berkontribusi untuk kebaikan."

                            <strong><span style="font-style: italic;">"Wa la tulkhu bi-aydikum ila tahlukah" (QS. Al-Baqarah: 195)</span></strong>
                          </p>
                          <a href="food-order.php" class="btn btn-sm btn-outline-primary">Food order</a>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4 d-flex justify-content-center">
                          <dotlottie-wc
                            src="https://lottie.host/efb1eaba-1520-4a60-8f0a-d16560862400/drNKIemkWn.lottie"
                            style="width: 170px; height: 170px;"
                            autoplay
                            loop
                          ></dotlottie-wc>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- News -->
                <div class="col-12 col-md-6">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">News</h5>
                        <small class="text-muted">Paragon News</small>
                      </div>
                      <div class="dropdown">
                        <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                          <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        </div>
                      </div>
                    </div>
                    <!-- Isi konten nantinya taro awah sini. (slide jpg) -->
                  </div>
                </div>
                <!--/ News -->
                <!-- Grafik -->
                <div class="col-12 col-md-6">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Grafik</h5>
                        <small class="text-muted">Makan VS Kupon</small>
                      </div>
                      <div class="dropdown">
                        <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                          <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div id="chart-makan-kupon"></div>
                      <?php
                      // Hitung total makan (order_menus.makan=1) dan total kupon (order_menus.kupon=1) untuk user ini
                      $total_makan = 0;
                      $total_kupon = 0;
                      if (isset($conn) && $conn instanceof mysqli && isset($_SESSION['user_id'])) {
                        $uid = (int)$_SESSION['user_id'];
                        // Total makan
                        $sqlMakan = "SELECT COUNT(*) AS total FROM order_menus om JOIN orders o ON om.order_id = o.id WHERE o.user_id = ? AND om.makan = 1";
                        if ($stmt = $conn->prepare($sqlMakan)) {
                          $stmt->bind_param('i', $uid);
                          $stmt->execute();
                          $res = $stmt->get_result();
                          if ($row = $res->fetch_assoc()) $total_makan = (int)$row['total'];
                          $stmt->close();
                        }
                        // Total kupon
                        $sqlKupon = "SELECT COUNT(*) AS total FROM order_menus om JOIN orders o ON om.order_id = o.id WHERE o.user_id = ? AND om.kupon = 1";
                        if ($stmt = $conn->prepare($sqlKupon)) {
                          $stmt->bind_param('i', $uid);
                          $stmt->execute();
                          $res = $stmt->get_result();
                          if ($row = $res->fetch_assoc()) $total_kupon = (int)$row['total'];
                          $stmt->close();
                        }
                      }
                      ?>
                      <script>
                      document.addEventListener('DOMContentLoaded', function() {
                        if (window.ApexCharts) {
                          var options = {
                            chart: { type: 'bar', height: 250 },
                            series: [{
                              name: 'Total',
                              data: [<?= $total_makan ?>, <?= $total_kupon ?>]
                            }],
                            xaxis: {
                              categories: ['Makan', 'Kupon'],
                              labels: { style: { fontSize: '14px' } }
                            },
                            colors: ['#00b894', '#e74c3c'],
                            plotOptions: { bar: { borderRadius: 6, columnWidth: '40%' } },
                            dataLabels: { enabled: true },
                            grid: { yaxis: { lines: { show: false } } },
                            legend: { show: false }
                          };
                          var chart = new ApexCharts(document.querySelector('#chart-makan-kupon'), options);
                          chart.render();
                        }
                      });
                      </script>
                    </div>
                  </div>
                </div>
                <!--/ Grafik -->
              </div>
            </div>
            <!-- / Content -->
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
  </body>
</html>
