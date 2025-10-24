
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Order History | ParaCanteen</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
  </head>
  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php include 'layout/sidebar.php'; ?>
        <div class="layout-page">
          <?php include 'layout/navbar.php'; ?>
          <div class="container mt-4">
            <div class="card shadow-sm p-4">
              <h4 class="mb-4"><i class="bi bi-clock-history"></i> Order History</h4>
              <div class="table-responsive">
                <table class="table table-bordered align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Year</th>
                      <th>Week</th>
                      <th>Plant</th>
                      <th>Place</th>
                      <th>Day</th>
                      <th>Menu</th>
                      <th>Date Ordered</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Dummy data, replace with database query -->
                    <tr>
                      <td>1</td>
                      <td>2025</td>
                      <td>1</td>
                      <td>Jatake 1</td>
                      <td>Kantin J1</td>
                      <td>Monday</td>
                      <td>Nasi Goreng</td>
                      <td>2025-09-30</td>
                    </tr>
                    <tr>
                      <td>2</td>
                      <td>2025</td>
                      <td>2</td>
                      <td>Jatake 6</td>
                      <td>Kantin J6 Depan</td>
                      <td>Tuesday</td>
                      <td>Nasi Kepal Merah</td>
                      <td>2025-10-01</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/dashboards-analytics.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>