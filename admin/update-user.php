<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; 

// Get all departments for dropdown
$dept_query = "SELECT * FROM department ORDER BY name ASC";
$dept_result = mysqli_query($conn, $dept_query);

// Get all plants for dropdown
$plant_query = "SELECT * FROM plant ORDER BY name ASC";
$plant_result = mysqli_query($conn, $plant_query);

// Get user data if ID is provided
$user_data = null;
if (isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_query = "SELECT u.*, u.departemen AS dept_name 
                   FROM users u
                   WHERE u.id = ?";
    $stmt = mysqli_prepare($conn, $user_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($user_result);
}

?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Update User Account | ParaCanteen</title>
    <meta name="description" content="" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
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
                        <h4 class="fw-bold py-3 mb-4">
                            <span class="text-muted fw-light">User Account /</span> Update User
                        </h4>

                        <?php if (!isset($_GET['id'])): ?>
                        <!-- User Selection Form -->
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Select User</h5>
                            </div>
                            <div class="card-body">
                                <form method="GET">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Search User</label>
                                            <select name="id" class="form-select" required onchange="this.form.submit()">
                                                <option value="">-- Select User --</option>
                                                <?php
                                                $users = mysqli_query($conn, "SELECT id, nama, nip FROM users ORDER BY nama ASC");
                                                while ($user = mysqli_fetch_assoc($users)) {
                                                    echo "<option value='{$user['id']}'>{$user['nama']} (NIP: {$user['nip']})</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($user_data): ?>
                        <!-- Update User Form -->
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Update User Information</h5>
                                <a href="update-user.php" class="btn btn-secondary btn-sm">
                                    <i class="bx bx-user-plus me-1"></i>
                                    Select Different User
                                </a>
                            </div>
                            <div class="card-body">
                                <form action="execution/user_action.php" method="POST">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($user_data['id']) ?>">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($user_data['nama']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">NIP</label>
                                            <input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($user_data['nip']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Gmail</label>
                                            <input type="email" class="form-control" name="gmail" value="<?= htmlspecialchars($user_data['gmail']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                                            <small class="text-muted">Fill only if you want to change the password</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Department</label>
                                            <select name="department_id" class="form-select" required>
                                                <option value="">Select Department</option>
                                                <?php
                                                mysqli_data_seek($dept_result, 0);
                                                while ($dept = mysqli_fetch_assoc($dept_result)) {
                                                    $selected = ($dept['id'] == $user_data['department_id']) ? 'selected' : '';
                                                    echo "<option value='{$dept['id']}' {$selected}>{$dept['name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Total Kupon</label>
                                            <input type="number" class="form-control" name="total_kupon" value="<?= htmlspecialchars($user_data['total_kupon']) ?>" required min="0">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="bx bx-save me-1"></i>
                                            Save Changes
                                        </button>
                                        <a href="update-user.php" class="btn btn-outline-secondary">
                                            <i class="bx bx-x me-1"></i>
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>

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

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
</body>
</html>
