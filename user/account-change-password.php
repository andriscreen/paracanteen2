<?php
// Autentikasi login
include "../auth.php";

// Cek role user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../form_login.php");
    exit;
}

// Koneksi database
require_once __DIR__ . '../config/db.php'; // pastikan path file db.php benar

// Ambil ID user dari session login
$userId = $_SESSION['user_id'] ?? null;

// Jika user ID tidak ditemukan di session, redirect
if (!$userId) {
    header("Location: ../form_login.php");
    exit;
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validasi input kosong
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo "Semua field wajib diisi!";
    }
    // Validasi password baru cocok
    elseif ($newPassword !== $confirmPassword) {
        echo "New password and confirm password do not match.";
    } else {
        // Ambil password lama dari database
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Jika user ditemukan
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            // Cek apakah password lama cocok (pakai MD5)
            if (md5($currentPassword) === $hashedPassword) {
                // Hash password baru dengan MD5 juga
                $newPasswordHashed = md5($newPassword);

                // Update password ke database
                $updateSql = "UPDATE users SET password = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("si", $newPasswordHashed, $userId);

                if ($updateStmt->execute()) {
                    echo "✅ Password berhasil diperbarui.";
                } else {
                    echo "❌ Gagal update password: " . $conn->error;
                }

                $updateStmt->close();
            } else {
                echo "❌ Password lama salah.";
            }
        } else {
            echo "User tidak ditemukan.";
        }

        $stmt->close();
    }
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
    <meta name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Change Password | ParaCanteen</title>

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

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
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
              <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Account Settings /</span> Change Password
              </h4>

              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link" href="account-settings.php"
                        ><i class="bx bx-user me-1"></i> Account</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"
                        ><i class="bx bx-key me-1"></i> Change Password</a
                      >
                    </li>
                  </ul>
                  <div class="card">
                    <h5 class="card-header">Change Password</h5>
                    <!-- Change Password -->
                    <div class="card-body">
                      <form id="formChangePassword" method="POST">
                          <div class="row">
                              <div class="mb-3 col-md-6">
                                  <label class="form-label" for="currentPassword">Current Password</label>
                                  <input class="form-control" type="password" id="currentPassword" name="currentPassword" placeholder="••••••" />
                              </div>
                          </div>
                          <div class="row">
                              <div class="mb-3 col-md-6">
                                  <label class="form-label" for="newPassword">New Password</label>
                                  <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="••••••" />
                              </div>
                          </div>
                          <div class="row">
                              <div class="mb-3 col-md-6">
                                  <label class="form-label" for="confirmPassword">Confirm New Password</label>
                                  <input class="form-control" type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••" />
                              </div>
                          </div>
                          <div class="mt-2">
                              <button type="submit" class="btn btn-primary me-2">Save changes</button>
                              <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                          </div>
                      </form>
                    </div>
                    <!-- /Change Password -->
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
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
