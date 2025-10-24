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

    <title>ParaCanteen - Food Order</title>

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

    <!-- Page CSS -->

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
      <div class="container mt-4">
        <?php
  include 'config/db.php';
        // Get years
        $years = [];
        $yearRes = $conn->query("SELECT * FROM year ORDER BY year_value DESC");
        while ($row = $yearRes->fetch_assoc()) {
          $years[] = $row;
        }
        // Get weeks
        $weeks = [];
        $weekRes = $conn->query("SELECT * FROM week ORDER BY week_number ASC");
        while ($row = $weekRes->fetch_assoc()) {
          $weeks[] = $row;
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
        // Get menus for default week (week_id = 1)
        $menus = [];
        $menuRes = $conn->query("SELECT * FROM menu WHERE week_id = 1 ORDER BY FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
        while ($row = $menuRes->fetch_assoc()) {
          $menus[] = $row;
        }
        ?>
        <div class="card shadow-sm p-4">
          <h4 class="mb-4"><i class="bi bi-calendar-week"></i> Select Week & Year</h4>
          <form method="post" action="">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Week</label>
              <select class="form-select" id="weekSelect" name="week">
                <?php foreach ($weeks as $week): ?>
                  <option value="<?= $week['id'] ?>">Week <?= $week['week_number'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Year</label>
              <select class="form-select" id="yearSelect" name="year">
                <?php foreach ($years as $year): ?>
                  <option value="<?= $year['id'] ?>"><?= $year['year_value'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Plant</label>
              <select class="form-select" id="plantSelect" name="plant">
                <?php foreach ($plants as $plant): ?>
                  <option value="<?= $plant['id'] ?>"><?= $plant['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Place</label>
              <select class="form-select" id="placeSelect" name="place">
                <?php foreach ($places as $place): ?>
                  <option value="<?= $place['id'] ?>"><?= $place['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mt-4">
            <div id="menuCards" class="row g-3">
              <?php foreach ($menus as $menu): ?>
                <div class="col-md-4">
                  <div class="card h-100 meal-card">
                    <div class="card-body">
                      <h5><?= $menu['day'] ?></h5>
                      <p><?= $menu['menu_name'] ?></p>
                      <div class="form-check mt-2">
                        <input class="form-check-input meal-checkbox" type="checkbox" name="menu_selected[]" value="<?= $menu['id'] ?>">
                        <label class="form-check-label">Select</label>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-between mt-4">
              <span id="summary"><b>0 days selected</b></span>
              <button class="btn btn-warning" type="submit">Save Order</button>
            </div>
          </div>
          </form>

<style>
    /* warna biru saat dipilih */
    .meal-card.selected {
        background-color: #696cff; /* biru bootstrap */
        color: white;
        transition: 0.3s;
    }
</style>

<script>
  // AJAX: Update menu cards when week changes
  document.getElementById('weekSelect').addEventListener('change', function() {
    var weekId = this.value;
    fetch('config/get_menus.php?week_id=' + weekId)
      .then(response => response.json())
      .then(data => {
        var menuRow = document.getElementById('menuCards');
        menuRow.innerHTML = '';
        data.forEach(function(menu) {
          var col = document.createElement('div');
          col.className = 'col-md-4';
          col.innerHTML = `<div class="card h-100 meal-card">
            <div class="card-body">
              <h5>${menu.day}</h5>
              <p>${menu.menu_name}</p>
              <div class="form-check mt-2">
                <input class="form-check-input meal-checkbox" type="checkbox" name="menu_selected[]" value="${menu.id}">
                <label class="form-check-label">Select</label>
              </div>
            </div>
          </div>`;
          menuRow.appendChild(col);
        });
        // Re-apply summary and card selection logic
        document.querySelectorAll('.meal-checkbox').forEach(function (checkbox) {
          checkbox.addEventListener('change', function () {
            const card = this.closest('.meal-card');
            if (this.checked) {
              card.classList.add('selected');
            } else {
              card.classList.remove('selected');
            }
            updateSummary();
          });
        });
        updateSummary();
      });
  });
  // Update summary saat checkbox berubah
  function updateSummary() {
    const checked = document.querySelectorAll('.meal-checkbox:checked').length;
    document.getElementById('summary').innerHTML = `<b>${checked} days selected</b>`;
  }
  document.querySelectorAll('.meal-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
      const card = this.closest('.meal-card');
      if (this.checked) {
        card.classList.add('selected');
      } else {
        card.classList.remove('selected');
      }
      updateSummary();
    });
  });
  // Inisialisasi summary saat halaman dimuat
  updateSummary();

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
