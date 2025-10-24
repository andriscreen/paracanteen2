<!DOCTYPE html>
<html>
<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/../auth.php';
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$nama = 'User';
$departemen = '';
$avatarPath = 'assets/img/avatars/1.png';
if ($user_id > 0 && isset($conn) && $conn instanceof mysqli) {
    if ($stmt = $conn->prepare('SELECT `nama`, `avatars`, `departemen` FROM `users` WHERE `id` = ? LIMIT 1')) {
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($res && $row = $res->fetch_assoc()) {
                $nama = $row['nama'] ?: $nama;
                $departemen = $row['departemen'] ?: '';
                $dbAvatar = isset($row['avatars']) ? trim($row['avatars']) : '';
                if ($dbAvatar !== '') {
                    $avatarPath = $dbAvatar;
                }
            }
        }
        $stmt->close();
    }
}
// Normalize avatar path relative to this file location (user/layout -> ../ to app root)
$avatarPath = trim($avatarPath);
if (preg_match('#^https?://#i', $avatarPath)) {
    // absolute URL, leave as is
} elseif (strpos($avatarPath, '../assets/') === 0) {
    // already correct relative to /user/* pages
} elseif (preg_match('#^(\./)?assets/#', $avatarPath)) {
    // convert ./assets/... or assets/... to ../assets/... (since user pages are one level deeper)
    $avatarPath = '../' . preg_replace('#^\./#', '', $avatarPath);
} elseif (strpos($avatarPath, '/assets/') === 0) {
    // root-relative path, leave as is
} else {
    // assume only filename provided
    $avatarPath = '../assets/img/avatars/' . basename($avatarPath);
}
// Fallback to default if file not found on filesystem
$projectRoot = dirname(dirname(__DIR__)); // paragonapp root
$checkPath = $avatarPath;
if (strpos($checkPath, '../') === 0) {
    $checkPath = substr($checkPath, 3); // remove leading ../ -> assets/...
}
$fsPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($checkPath, '/'));
if (!is_file($fsPath)) {
    $avatarPath = '../assets/img/avatars/1.png';
}
?>
          
          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input
                    type="text"
                    class="form-control border-0 shadow-none"
                    placeholder="Search..."
                    aria-label="Search..."
                  />
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <?php
                // Ambil total kupon user
                $total_kupon_user = 0;
                if ($user_id > 0 && isset($conn) && $conn instanceof mysqli) {
                    if ($stmt2 = $conn->prepare('SELECT total_kupon FROM users WHERE id = ? LIMIT 1')) {
                        $stmt2->bind_param('i', $user_id);
                        if ($stmt2->execute()) {
                            $res2 = $stmt2->get_result();
                            if ($res2 && $row2 = $res2->fetch_assoc()) {
                                $total_kupon_user = (int)$row2['total_kupon'];
                            }
                        }
                        $stmt2->close();
                    }
                }
                ?>
                <li class="nav-item lh-1 me-3">
                  <a href="#" class="btn btn-outline-primary d-flex align-items-center position-relative">
                    <i class="bi bi-ticket-perforated-fill me-1"></i> coupons
                    <span class="badge bg-danger ms-2"><?= $total_kupon_user ?></span>
                  </a>
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="<?= $avatarPath ?>" alt="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>" class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="<?= $avatarPath ?>" alt="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>" class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block"><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?></span>
                            <small class="text-muted"><?= htmlspecialchars($departemen, ENT_QUOTES, 'UTF-8') ?></small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="account-settings.php">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="account-change-password.php">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="logout.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>
</html>