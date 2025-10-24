<?php include "../auth.php"; ?>
<?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { header("Location: ../form_login.php"); exit; } ?>
<?php
require_once __DIR__ . '/config/db.php';
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$nama = 'User';
$nip = '';
$gmail = '';
$departemen = '';
$avatarPath = '../assets/img/avatars/1.png';
if ($user_id > 0 && isset($conn) && $conn instanceof mysqli) {
    if ($stmt = $conn->prepare('SELECT `nama`, `nip`, `gmail`, `avatars`, `departemen` FROM `users` WHERE `id` = ? LIMIT 1')) {
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($res && $row = $res->fetch_assoc()) {
                $nama = $row['nama'] ?: $nama;
                $nip = $row['nip'] ?: '';
                $gmail = $row['gmail'] ?: '';
                $departemen = $row['departemen'] ?: '';
                $dbAvatar = isset($row['avatars']) ? trim($row['avatars']) : '';
                if ($dbAvatar !== '') { $avatarPath = $dbAvatar; }
            }
        }
        $stmt->close();
    }
}
$avatarPath = trim($avatarPath);
if (preg_match('#^https?://#i', $avatarPath)) {
} elseif (strpos($avatarPath, '../assets/') === 0) {
} elseif (preg_match('#^(\./)?assets/#', $avatarPath)) {
    $avatarPath = '../' . preg_replace('#^\./#', '', $avatarPath);
} elseif (strpos($avatarPath, '/assets/') === 0) {
} else {
    $avatarPath = '../assets/img/avatars/' . basename($avatarPath);
}
$projectRoot = dirname(__DIR__);
$checkPath = $avatarPath;
if (strpos($checkPath, '../') === 0) { $checkPath = substr($checkPath, 3); }
$fsPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($checkPath, '/'));
if (!is_file($fsPath)) { $avatarPath = '../assets/img/avatars/1.png'; }

// Load department options (supports either `department(name)` or `departemen(nama)` tables). Fallback to static list if none found.
$departemenOptions = [];
if (isset($conn) && $conn instanceof mysqli) {
    $queries = [
        "SELECT id, name AS nama FROM department ORDER BY name ASC",
        "SELECT id, nama FROM departemen ORDER BY nama ASC",
    ];
    foreach ($queries as $q) {
        if ($res = $conn->query($q)) {
            while ($row = $res->fetch_assoc()) {
                if (!empty($row['nama'])) { $departemenOptions[] = $row['nama']; }
            }
            $res->free();
            if (!empty($departemenOptions)) { break; }
        }
    }
}
if (empty($departemenOptions)) {
    $departemenOptions = [
        'Facility Management', 'Human Resources', 'Finance', 'IT', 'Production',
        'Quality Assurance', 'Marketing', 'Sales', 'Operations', 'Procurement'
    ];
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

    <title>Account settings - Account | ParaCanteen</title>

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
    <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css" />
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
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Account</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="account-change-password.php"
                        ><i class="bx bx-key me-1"></i> Change Password</a
                      >
                    </li>
                  </ul>
                  <div class="card mb-4">
                    <h5 class="card-header">Profile Details</h5>
                    <!-- Account -->
                    <div class="card-body">
                      <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                          src="<?= $avatarPath ?>"
                          alt="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                          class="d-block rounded"
                          height="100"
                          width="100"
                          id="uploadedAvatar"
                        />
                        <div class="button-wrapper">
                          <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="bx bx-upload d-block d-sm-none"></i>
                            <!-- File input is provided within the form below to ensure it is submitted -->
                          </label>
                          <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                            <i class="bx bx-reset d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                          </button>

                          <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 2 MB</p>
                        </div>
                      </div>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                      <form id="formAccountSettings" method="POST" action="execution/account-update.php" enctype="multipart/form-data">
                        <input type="file" id="upload" name="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                        <div class="row">
                          <div class="mb-3 col-md-6">
                            <label for="nama" class="form-label">Nama</label>
                            <input
                              class="form-control"
                              type="text"
                              id="nama"
                              name="nama"
                              value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                              autofocus
                            />
                          </div>
                          <div class="mb-3 col-md-6">
                            <label for="gmail" class="form-label">Gmail</label>
                            <input class="form-control" type="email" name="gmail" id="gmail" value="<?= htmlspecialchars($gmail, ENT_QUOTES, 'UTF-8') ?>" />
                          </div>
                          <div class="mb-3 col-md-6">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" value="<?= htmlspecialchars($nip, ENT_QUOTES, 'UTF-8') ?>" inputmode="numeric" pattern="\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'')" />
                          </div>
                          <div class="mb-3 col-md-6">
                            <label for="departemen" class="form-label">Departemen</label>
                            <select id="departemen" name="departemen" class="form-select">
                              <option value="">Select Departemen</option>
                              <?php foreach ($departemenOptions as $dep): $sel = ($dep === $departemen) ? ' selected' : ''; ?>
                                <option value="<?= htmlspecialchars($dep, ENT_QUOTES, 'UTF-8') ?>"<?= $sel ?>><?= htmlspecialchars($dep, ENT_QUOTES, 'UTF-8') ?></option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        <div class="mt-2">
                          <div id="successAlert" class="alert alert-success d-none" role="alert">
                              Perubahan berhasil disimpan!
                          </div>
                          <button type="submit" class="btn btn-primary me-2">Save changes</button>
                          <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                      </form>
                    </div>
                    <!-- /Account -->
                  </div>
                  <div class="card">
                    <h5 class="card-header">Delete Account</h5>
                    <div class="card-body">
                      <div class="mb-3 col-12 mb-0">
                        <div class="alert alert-warning">
                          <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                          <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                      </div>
                      <form id="formAccountDeactivation" method="POST" action="execution/account-delete.php" onsubmit="return confirmDelete(event)">
                        <div class="form-check mb-3">
                          <input
                            class="form-check-input"
                            type="checkbox"
                            name="accountActivation"
                            id="accountActivation"
                            required
                          />
                          <label class="form-check-label" for="accountActivation">
                            I confirm my account deletion
                          </label>
                        </div>
                        <button type="submit" class="btn btn-danger deactivate-account">Delete Account</button>
                      </form>

                      <script>
                        function confirmDelete(e) {
                          if (!document.getElementById('accountActivation').checked) {
                            alert('Silakan centang konfirmasi terlebih dahulu.');
                            e.preventDefault();
                            return false;
                          }
                          const ok = confirm('Apakah Anda sudah yakin akan menghapus akun ini?');
                          if (!ok) {
                            e.preventDefault();
                            return false;
                          }
                          return true;
                        }
                      </script>
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

    <!-- Cropper Modal -->
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Crop Avatar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="img-container">
              <img id="cropperImage" alt="To crop" style="max-width: 100%; display: block;">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="applyCrop">Crop & Apply</button>
          </div>
        </div>
      </div>
    </div>

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
    <script src="../assets/js/pages-account-settings-account.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const uploadInput = document.getElementById('upload');
        const modalEl = document.getElementById('cropperModal');
        const imgEl = document.getElementById('cropperImage');
        const previewEl = document.getElementById('uploadedAvatar');
        let cropper;
        let objectUrl;

        if (!uploadInput) return;

        uploadInput.addEventListener('change', function(e) {
          const file = e.target.files && e.target.files[0];
          if (!file) return;
          if (!/^image\/(png|jpe?g|gif)$/i.test(file.type)) {
            alert('Tipe file tidak diizinkan. Hanya JPG, PNG, GIF.');
            uploadInput.value = '';
            return;
          }
          objectUrl = URL.createObjectURL(file);
          imgEl.src = objectUrl;

          const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
          const onShown = function() {
            modalEl.removeEventListener('shown.bs.modal', onShown);
            cropper = new Cropper(imgEl, {
              aspectRatio: 1,
              viewMode: 1,
              autoCropArea: 1,
              movable: true,
              zoomable: true,
              responsive: true
            });
          };
          const onHidden = function() {
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            if (cropper) { cropper.destroy(); cropper = null; }
            if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
            if (!document.getElementById('applyCrop').dataset.applied) {
              uploadInput.value = '';
            }
            document.getElementById('applyCrop').dataset.applied = '';
          };
          modalEl.addEventListener('shown.bs.modal', onShown);
          modalEl.addEventListener('hidden.bs.modal', onHidden);
          bsModal.show();
        });

        const applyBtn = document.getElementById('applyCrop');
        applyBtn.addEventListener('click', function() {
          if (!cropper) return;
          const that = this;
          const canvas = cropper.getCroppedCanvas({ width: 600, height: 600, imageSmoothingQuality: 'high' });
          canvas.toBlob(function(blob) {
            if (!blob) return;
            const file = new File([blob], 'avatar.png', { type: 'image/png' });
            const dt = new DataTransfer();
            dt.items.add(file);
            uploadInput.files = dt.files;
            // Update preview image
            const reader = new FileReader();
            reader.onload = function(ev) { previewEl.src = ev.target.result; };
            reader.readAsDataURL(file);
            that.dataset.applied = '1';
            bootstrap.Modal.getInstance(modalEl).hide();
          }, 'image/png', 1);
        });
      });
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Menambahkan Alart untuk tombol Save Changes -->
<script>
document.querySelector('#formAccountSettings').addEventListener('submit', function(event) {
  event.preventDefault(); // Mencegah form langsung submit

  // Tampilkan alert
  const alertBox = document.getElementById('successAlert');
  alertBox.classList.remove('d-none');

  // Setelah 3 detik, submit form
  setTimeout(() => {
    alertBox.classList.add('d-none');
    this.submit(); // Submit form secara manual
  }, 3000); // Delay 3 detik
});
</script>
  </body>
</html>
