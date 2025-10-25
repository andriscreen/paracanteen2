<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'guest') { 
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

    <title>Guest Dashboard - ParaCanteen</title>
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
              <!-- Welcome Section -->
              <div class="row mb-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body text-center py-5">
                      <h2 class="card-title text-primary mb-3"><strong>Selamat Datang di ParaCanteen!</strong></h2>
                      <p class="card-text fs-5">
                        Halo <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>, selamat datang sebagai tamu di sistem kantin PT. Paragon Technology & Innovation
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- About Paragon Section -->
                <div class="col-lg-8 mb-4">
                  <div class="card">
                    <div class="card-header">
                      <h4 class="card-title mb-0">
                        <i class="bx bx-building-house me-2"></i>
                        Tentang <strong>PT. Paragon Technology & Innovation</strong>
                      </h4>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-4 text-center mb-3">
                          <img
                            src="../assets/img/illustrations/paragon-building.png"
                            alt="Paragon Building"
                            class="img-fluid rounded"
                            style="max-height: 200px;"
                            onerror="this.src='../assets/img/illustrations/man-with-laptop-light.png'"
                          />
                        </div>
                        <div class="col-md-8">
                          <h5 class="text-primary">Visi Perusahaan</h5>
                          <p class="mb-3">
                            "Menjadi perusahaan kosmetik terdepan yang menginspirasi dan memberdayakan perempuan Indonesia untuk meraih kecantikan seutuhnya."
                          </p>
                          
                          <h5 class="text-primary">Misi Perusahaan</h5>
                          <ul class="mb-3">
                            <li>Menghasilkan produk kosmetik berkualitas tinggi dengan inovasi terdepan</li>
                            <li>Memberikan nilai tambah bagi seluruh stakeholder</li>
                            <li>Mengembangkan potensi manusia Indonesia</li>
                            <li>Beroperasi dengan prinsip-prinsip bisnis yang beretika dan berkelanjutan</li>
                          </ul>

                          <h5 class="text-primary">Brand Unggulan</h5>
                          <div class="row">
                            <div class="col-sm-6">
                              <ul>
                                <li><strong>Wardah</strong> - Kosmetik halal</li>
                                <li><strong>Emina</strong> - Kosmetik untuk remaja</li>
                                <li><strong>Make Over</strong> - Professional makeup</li>
                              </ul>
                            </div>
                            <div class="col-sm-6">
                              <ul>
                                <li><strong>Kahf</strong> - Personal care pria</li>
                                <li><strong>Biodef</strong> - Perawatan Tubuh</li>
                                <li><strong>OMG</strong> - Beginner friendly makeup</li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Company Values -->
                  <div class="card mt-4">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-star me-2"></i>
                        Nilai-Nilai Perusahaan (SPIRIT)
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="d-flex">
                            <div class="flex-shrink-0">
                              <span class="badge bg-primary me-3">S</span>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Synergy</h6>
                              <p class="mb-0 text-muted">Bekerja sama untuk mencapai tujuan bersama</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="d-flex">
                            <div class="flex-shrink-0">
                              <span class="badge bg-success me-3">P</span>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Proactive</h6>
                              <p class="mb-0 text-muted">Antisipatif dan inisiatif dalam bertindak</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="d-flex">
                            <div class="flex-shrink-0">
                              <span class="badge bg-info me-3">I</span>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Integrity</h6>
                              <p class="mb-0 text-muted">Jujur dan konsisten dalam perkataan dan perbuatan</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="d-flex">
                            <div class="flex-shrink-0">
                              <span class="badge bg-warning me-3">R</span>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Responsive</h6>
                              <p class="mb-0 text-muted">Cepat tanggap terhadap perubahan dan kebutuhan</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="d-flex">
                            <div class="flex-shrink-0">
                              <span class="badge bg-danger me-3">I</span>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Innovation</h6>
                              <p class="mb-0 text-muted">Terus berinovasi untuk kemajuan</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="d-flex">
                            <div class="flex-shrink-0">
                              <span class="badge bg-dark me-3">T</span>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Trust</h6>
                              <p class="mb-0 text-muted">Membangun kepercayaan dengan semua pihak</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Sidebar Info -->
                <div class="col-lg-4">
                  <!-- Time & Date Card -->
                  <div class="card mb-4">
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
                    </div>
                  </div>

                  <!-- Date & Week Card -->
                  <div class="card mb-4">
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
                      <span class="fw-semibold d-block mb-1">Tanggal & Minggu</span>
                      <h3 class="card-title text-nowrap mb-1" id="compactDate">Loading...</h3>
                      <small class="text-info fw-semibold"><i class="bx bx-calendar-week"></i> <span id="weekDisplay">Minggu</span></small>
                    </div>
                  </div>

                  <!-- Quick Info -->
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-info-circle me-2"></i>
                        Informasi Cepat
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <h6 class="text-primary">Lokasi Kantor</h6>
                        <p class="mb-1 small">Jl. Raya Jatake No.1, Jatake, Kec. Jatiuwung, Kota Tangerang, Banten 15136</p>
                      </div>
                      <div class="mb-3">
                        <h6 class="text-primary">Industri</h6>
                        <p class="mb-1 small">Kosmetik & Personal Care</p>
                      </div>
                      <div>
                        <h6 class="text-primary">Didirikan</h6>
                        <p class="mb-1 small">1985</p>
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

    <script>
      // Live Clock
      function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID');
        document.getElementById('liveClock').textContent = time;
      }

      // Date & Week
      function updateCompactDateTime() {
        const now = new Date();
        const date = now.toLocaleDateString('id-ID', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
        
        function getWeekNumber(d) {
          d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
          d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
          const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
          const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
          return weekNo;
        }
        
        const weekNumber = getWeekNumber(now);
        const dayName = now.toLocaleDateString('id-ID', { weekday: 'long' });
        
        document.getElementById('compactDate').textContent = now.toLocaleDateString('id-ID');
        document.getElementById('weekDisplay').textContent = `Minggu ${weekNumber} - ${dayName}`;
      }

      // Initialize
      updateClock();
      updateCompactDateTime();
      setInterval(updateClock, 1000);
    </script>
  </body>
</html>