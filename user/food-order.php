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
        <?php
        include 'config/db.php';
        
        // Get current week and year
        $currentWeek = date('W');
        $currentYear = date('Y');
        
        // Get current week ID from database
        $currentWeekId = 1; // default
        $weekRes = $conn->query("SELECT id FROM week WHERE week_number = $currentWeek");
        if ($weekRes->num_rows > 0) {
            $weekRow = $weekRes->fetch_assoc();
            $currentWeekId = $weekRow['id'];
        }
        
        // Get current year ID from database
        $currentYearId = 1; // default
        $yearRes = $conn->query("SELECT id FROM year WHERE year_value = $currentYear");
        if ($yearRes->num_rows > 0) {
            $yearRow = $yearRes->fetch_assoc();
            $currentYearId = $yearRow['id'];
        }

        // Get plants
        $plants = [];
        $plantRes = $conn->query("SELECT * FROM plant ORDER BY name ASC");
        while ($row = $plantRes->fetch_assoc()) {
          $plants[] = $row;
        }
        // Get places (default: first plant)
        $places = [];
        $defaultPlantId = isset($plants[0]['id']) ? $plants[0]['id'] : 1;
        $placeRes = $conn->query("SELECT * FROM place WHERE plant_id = $defaultPlantId ORDER BY name ASC");
        while ($row = $placeRes->fetch_assoc()) {
          $places[] = $row;
        }
        // Get shifts
        $shifts = [];
        $shiftRes = $conn->query("SELECT * FROM shift ORDER BY id ASC");
        while ($row = $shiftRes->fetch_assoc()) {
          $shifts[] = $row;
        }
        
        // Define days of the week
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        ?>
        <div class="card shadow-sm p-4">
          <h4 class="mb-4"><i class="bi bi-calendar-week"></i> Food Order - Week <?= $currentWeek ?>, <?= $currentYear ?></h4>
          <form method="post" action="execution/process_order.php">
              <div class="row g-3">
                  <!-- Week (Hidden but passed in form) -->
                  <input type="hidden" id="weekSelect" name="week" value="<?= $currentWeekId ?>">
                  
                  <!-- Year (Hidden but passed in form) -->
                  <input type="hidden" id="yearSelect" name="year" value="<?= $currentYearId ?>">
                  
                  <!-- Display current week and year (readonly) -->
                  <div class="col-md-6">
                      <label class="form-label">Week</label>
                      <input type="text" class="form-control" value="Week <?= $currentWeek ?>" readonly>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">Year</label>
                      <input type="text" class="form-control" value="<?= $currentYear ?>" readonly>
                  </div>

                  <!-- Plant -->
                  <div class="col-md-6">
                      <label class="form-label">Plant</label>
                      <select class="form-select" id="plantSelect" name="plant">
                          <?php foreach ($plants as $plant): ?>
                              <option value="<?= $plant['id'] ?>"><?= $plant['name'] ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <!-- Place -->
                  <div class="col-md-6">
                      <label class="form-label">Place</label>
                      <select class="form-select" id="placeSelect" name="place">
                          <?php foreach ($places as $place): ?>
                              <option value="<?= $place['id'] ?>"><?= $place['name'] ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <!-- Shift -->
                  <div class="col-md-6">
                      <label class="form-label">Shift</label>
                      <select class="form-select" id="shiftSelect" name="shift">
                          <?php foreach ($shifts as $shift): ?>
                              <option value="<?= $shift['id'] ?>"><?= $shift['nama_shift'] ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
              </div>

              <div class="mt-4">
                  <div id="menuCards" class="row g-3">
                      <?php foreach ($days as $day): ?>
                          <div class="col-md-4">
                              <div class="card h-100 meal-card" data-day="<?= $day ?>">
                                  <div class="card-body">
                                      <h5><?= $day ?></h5>
                                      
                                      <!-- Makan -->
                                      <div class="form-check mt-2">
                                          <input class="form-check-input meal-checkbox" type="checkbox" 
                                                 name="makan_<?= strtolower($day) ?>" value="1" 
                                                 id="makan_<?= strtolower($day) ?>">
                                          <label class="form-check-label" for="makan_<?= strtolower($day) ?>">Makan</label>
                                      </div>

                                      <!-- Kupon -->
                                      <div class="form-check">
                                          <input class="form-check-input" type="checkbox" 
                                                 name="kupon_<?= strtolower($day) ?>" value="1" 
                                                 id="kupon_<?= strtolower($day) ?>">
                                          <label class="form-check-label" for="kupon_<?= strtolower($day) ?>">Kupon</label>
                                      </div>

                                      <!-- Libur -->
                                      <div class="form-check">
                                          <input class="form-check-input" type="checkbox" 
                                                 name="libur_<?= strtolower($day) ?>" value="1" 
                                                 id="libur_<?= strtolower($day) ?>">
                                          <label class="form-check-label" for="libur_<?= strtolower($day) ?>">Libur</label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      <?php endforeach; ?>
                  </div>
                  <div class="d-flex justify-content-between mt-4">
                      <span id="summary"><b>0 days selected</b></span>
                      <button id="saveOrderBtn" class="btn btn-warning" type="submit" disabled>Save Order</button>
                  </div>

                  <!-- Modal Konfirmasi -->
                  <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" aria-hidden="false">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="confirmOrderModalLabel">Konfirmasi Order</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row mb-3">
                            <div class="col-md-6">
                              <h6>Detail Order:</h6>
                              <p><strong>Plant:</strong> <span id="confirmPlant"></span></p>
                              <p><strong>Place:</strong> <span id="confirmPlace"></span></p>
                              <p><strong>Week:</strong> Week <?= $currentWeek ?></p>
                              <p><strong>Year:</strong> <?= $currentYear ?></p>
                              <p><strong>Shift:</strong> <span id="confirmShift"></span></p>
                            </div>
                            <div class="col-md-6">
                              <h6>Ringkasan Order:</h6>
                              <p><strong>Total Hari Makan:</strong> <span id="confirmMakan">0</span></p>
                              <p><strong>Total Kupon:</strong> <span id="confirmKupon">0</span></p>
                              <p><strong>Total Hari Libur:</strong> <span id="confirmLibur">0</span></p>
                            </div>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-bordered">
                              <thead>
                                <tr>
                                  <th>Hari</th>
                                  <th>Status</th>
                                </tr>
                              </thead>
                              <tbody id="confirmMenuList">
                              </tbody>
                            </table>
                          </div>
                          <div class="alert alert-warning mt-3">
                            <i class="bx bx-info-circle me-2"></i>
                            Pastikan data order sudah benar sebelum menyimpan.
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>
                            Batal
                          </button>
                          <button type="button" class="btn btn-primary" id="confirmOrderBtn">
                            <i class="bx bx-check me-1"></i>
                            Ya, Simpan Order
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Modal Alert -->
                  <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Perhatian</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <p id="alertMessage">Silakan pilih status untuk semua hari terlebih dahulu!</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
          </form>
      </div>

<style>
    /* Warna default */
    .meal-card {
        transition: background-color 0.3s, color 0.3s;
        color: black;
    }

    /* Makan - biru */
    .meal-card.makan-selected {
        background-color: #696cff; /* biru bootstrap */
        color: white;
    }

    /* Kupon - merah */
    .meal-card.kupon-selected {
        background-color: #dc3545; /* merah bootstrap */
        color: white;
    }

    /* Libur - abu-abu */
    .meal-card.libur-selected {
        background-color: #6c757d; /* abu-abu bootstrap */
        color: white;
    }
</style>

<script>
document.querySelectorAll('.meal-card').forEach(card => {
  const checkboxes = card.querySelectorAll('input[type="checkbox"]');
  
  checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      if (cb.checked) {
        // uncheck semua checkbox lain dalam card selain ini
        checkboxes.forEach(otherCb => {
          if (otherCb !== cb) otherCb.checked = false;
        });
        
        // update warna card sesuai checkbox yang dicentang
        updateCardColor(card);
        updateSummary();
      } else {
        // kalau dicentang jadi unchecked, update warna card
        updateCardColor(card);
        updateSummary();
      }
    });
  });
});

// Fungsi update warna card
function updateCardColor(card) {
  card.classList.remove('makan-selected', 'kupon-selected', 'libur-selected');
  
  const day = card.dataset.day;
  const makanChecked = document.getElementById('makan_' + day.toLowerCase()).checked;
  const kuponChecked = document.getElementById('kupon_' + day.toLowerCase()).checked;
  const liburChecked = document.getElementById('libur_' + day.toLowerCase()).checked;
  
  if (makanChecked) {
    card.classList.add('makan-selected');
  } else if (kuponChecked) {
    card.classList.add('kupon-selected');
  } else if (liburChecked) {
    card.classList.add('libur-selected');
  }
}

// Inisialisasi warna saat halaman dimuat
document.querySelectorAll('.meal-card').forEach(updateCardColor);
</script>

<script>
    // Fungsi untuk mengecek apakah semua hari sudah dicentang
    function checkAllDaysSelected() {
      const mealCards = document.querySelectorAll('.meal-card');
      let allSelected = true;
      
      mealCards.forEach(card => {
        const hasSelection = Array.from(card.querySelectorAll('input[type="checkbox"]')).some(cb => cb.checked);
        if (!hasSelection) {
          allSelected = false;
        }
      });

      const saveBtn = document.getElementById('saveOrderBtn');
      saveBtn.disabled = !allSelected;
      
      return allSelected;
    }

    // Event listener untuk setiap perubahan checkbox
    document.querySelectorAll('.meal-card input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener('change', checkAllDaysSelected);
    });

    // Event listener untuk tombol Save Order
    document.getElementById("saveOrderBtn").addEventListener("click", function(event) {
      event.preventDefault();
      
      if (!checkAllDaysSelected()) {
        // Tampilkan modal alert jika belum semua hari dicentang
        new bootstrap.Modal(document.getElementById('alertModal')).show();
        return;
      }

      // Isi detail konfirmasi
      document.getElementById('confirmPlant').textContent = document.getElementById('plantSelect').options[document.getElementById('plantSelect').selectedIndex].text;
      document.getElementById('confirmPlace').textContent = document.getElementById('placeSelect').options[document.getElementById('placeSelect').selectedIndex].text;
      document.getElementById('confirmShift').textContent = document.getElementById('shiftSelect').options[document.getElementById('shiftSelect').selectedIndex].text;

      // Hitung total setiap jenis
      let totalMakan = 0;
      let totalKupon = 0;
      let totalLibur = 0;
      
      // Clear tabel menu
      const menuList = document.getElementById('confirmMenuList');
      menuList.innerHTML = '';

      // Isi tabel menu dan hitung total
      document.querySelectorAll('.meal-card').forEach(card => {
        const day = card.querySelector('h5').textContent;
        let status = '';

        if (document.getElementById('makan_' + day.toLowerCase()).checked) {
          status = 'Makan';
          totalMakan++;
        } else if (document.getElementById('kupon_' + day.toLowerCase()).checked) {
          status = 'Kupon';
          totalKupon++;
        } else if (document.getElementById('libur_' + day.toLowerCase()).checked) {
          status = 'Libur';
          totalLibur++;
        }

        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${day}</td>
          <td><span class="badge bg-${status === 'Makan' ? 'primary' : status === 'Kupon' ? 'danger' : 'secondary'}">${status}</span></td>
        `;
        menuList.appendChild(row);
      });

      // Update totals
      document.getElementById('confirmMakan').textContent = totalMakan;
      document.getElementById('confirmKupon').textContent = totalKupon;
      document.getElementById('confirmLibur').textContent = totalLibur;

      // Tampilkan modal konfirmasi
      new bootstrap.Modal(document.getElementById('confirmOrderModal')).show();
    });

    // Event listener untuk tombol konfirmasi di modal
    document.getElementById("confirmOrderBtn").addEventListener("click", function() {
      // Sembunyikan modal konfirmasi
      bootstrap.Modal.getInstance(document.getElementById('confirmOrderModal')).hide();
      
      // Submit form
      document.querySelector('form').submit();
    });
</script>

<script>
  // AJAX: Update place dropdown when plant changes
  document.getElementById('plantSelect').addEventListener('change', function() {
    var plantId = this.value;
  fetch('config/get_places.php?plant_id=' + plantId)
      .then(response => response.json())
      .then(data => {
        var placeSelect = document.getElementById('placeSelect');
        placeSelect.innerHTML = '';
        data.forEach(function(place) {
          var opt = document.createElement('option');
          opt.value = place.id;
          opt.textContent = place.name;
          placeSelect.appendChild(opt);
        });
      });
  });
</script>

<script>
// Update summary saat checkbox berubah
function updateSummary() {
  let count = 0;
  document.querySelectorAll('#menuCards .meal-card').forEach(card => {
    if (card.querySelector('input[type="checkbox"]:checked')) count++;
  });
  document.getElementById('summary').innerHTML = `<b>${count} days selected</b>`;
}

// Inisialisasi summary saat halaman dimuat
updateSummary();
</script>
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