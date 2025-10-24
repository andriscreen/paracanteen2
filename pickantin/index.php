<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'pickantin') { 
    header("Location: ../form_login.php"); 
    exit; 
} ?>
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
                <div class="col-lg-8 mb-4 order-0">
                  <div class="card">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h3 class="card-title text-primary fw-bold">Hallo Admin Facility Service! 😏</h3>
                          <p class="mb-4">
                            "<strong>Berdoalah</strong> sebelum memulai pekerjaan. Minta kepada <strong>Allah.SWT</strong> agar diberi kemudahan dan keberkahan dalam setiap langkah yang kita ambil."
                            <strong><span style="font-style: italic;">"Rabbana la tuzigh qulubana ba'da iz hadaitanna wahab lana milladunka rahmatan innaka antal wahhab" (QS. Al-Imran: 8)</span></strong>
                          </p>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                          <img
                            src="../assets/img/illustrations/man-with-laptop-light.png"
                            height="140"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-4 col-md-4 order-1">
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <img
                                src="../assets/img/icons/unicons/chart-success.png"
                                alt="chart success"
                                class="rounded"
                              />
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Waktu Sekarang</span>
                            <h3 class="card-title mb-2" id="liveClock">00:00:00</h3>
                            <small class="text-success fw-semibold"><i class="bx bx-time"></i> Live</small>

                            <script>
                            function updateClock() {
                                const now = new Date();
                                const time = now.toLocaleTimeString('id-ID');
                                document.getElementById('liveClock').textContent = time;
                            }

                            // Update setiap detik
                            updateClock();
                            setInterval(updateClock, 1000);
                            </script>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <img
                                src="../assets/img/icons/unicons/wallet-info.png"
                                alt="Credit Card"
                                class="rounded"
                              />
                            </div>
                          </div>
                            <span class="fw-semibold d-block mb-1">Date & Week</span>
                            <h3 class="card-title text-nowrap mb-1" id="compactDate">Loading...</h3>
                            <small class="text-info fw-semibold"><i class="bx bx-calendar-week"></i> <span id="weekDisplay">Week</span></small>

                            <script>
                            function updateCompactDateTime() {
                                const now = new Date();
                                
                                // Format compact: DD/MM/YYYY
                                const date = now.toLocaleDateString('id-ID');
                                
                                // Week number
                                function getWeekNumber(d) {
                                    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
                                    d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
                                    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
                                    const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
                                    return weekNo;
                                }
                                
                                const weekNumber = getWeekNumber(now);
                                
                                document.getElementById('compactDate').textContent = date;
                                document.getElementById('weekDisplay').textContent = `Week ${weekNumber}`;
                            }

                            updateCompactDateTime();
                            </script>
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

    <!-- GitHub buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>