<?php 
include "../auth.php";
if ($_SESSION['role'] !== 'admin') { 
    header("Location: ../form_login.php"); 
    exit; 
} 
include 'config/db.php'; 

// Alert messages
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Ambil data week dan vendor aktif
$query = "SELECT DISTINCT id, week_number FROM week ORDER BY week_number ASC";
$result = mysqli_query($conn, $query);

// Ambil data vendor
$vendorQuery = "SELECT id, name FROM nama_vendor WHERE is_active = 1 ORDER BY name ASC";
$vendorResult = mysqli_query($conn, $vendorQuery);
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Update Menu | ParaCanteen</title>
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
                        
                        <!-- Alert Messages -->
                        <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Update Menu</h4>

                        <!-- Basic Layout -->
                        <div class="row">
                            <!-- Form Tambah Menu -->
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Add/Update Menu</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="execution/menu_action.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="add">
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Week</label>
                                                        <select name="week_id" class="form-select" required>
                                                            <option value="">-- Select Week --</option>
                                                            <?php
                                                            $weeks = mysqli_query($conn, "SELECT id, week_number FROM week ORDER BY week_number ASC");
                                                            while($week = mysqli_fetch_assoc($weeks)) {
                                                                echo "<option value='{$week['id']}'>Week {$week['week_number']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Vendor</label>
                                                        <select name="vendor_id" class="form-select" required>
                                                            <option value="">-- Select Vendor --</option>
                                                            <?php
                                                            mysqli_data_seek($vendorResult, 0);
                                                            while($vendor = mysqli_fetch_assoc($vendorResult)) {
                                                                echo "<option value='{$vendor['id']}'>{$vendor['name']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Day</label>
                                                <select name="day" class="form-select" required>
                                                    <option value="">-- Select Day --</option>
                                                    <option value="Senin">Senin</option>
                                                    <option value="Selasa">Selasa</option>
                                                    <option value="Rabu">Rabu</option>
                                                    <option value="Kamis">Kamis</option>
                                                    <option value="Jumat">Jumat</option>
                                                    <option value="Sabtu">Sabtu</option>
                                                    <option value="Minggu">Minggu</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Menu Name</label>
                                                <input type="text" name="menu_name" class="form-control" placeholder="Enter menu name" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea name="keterangan" class="form-control" placeholder="Enter keterangan (optional)" rows="2"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Menu Image</label>
                                                <input type="file" name="menu_image" class="form-control" accept="image/*">
                                                <small class="text-muted">Upload image for the menu (JPG, PNG). Optional. Will replace existing image if any.</small>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Save Menu</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Hapus Menu -->
                            <div class="col-xl">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Delete Menu</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="execution/menu_action.php" method="POST">
                                            <input type="hidden" name="action" value="delete">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Week</label>
                                                        <select name="week_id" class="form-select" id="deleteWeekSelect" required>
                                                            <option value="">-- Select Week --</option>
                                                            <?php
                                                            $weeks = mysqli_query($conn, "SELECT DISTINCT m.week_id, w.week_number 
                                                                                        FROM menu m 
                                                                                        JOIN week w ON m.week_id = w.id 
                                                                                        ORDER BY w.week_number ASC");
                                                            while($week = mysqli_fetch_assoc($weeks)) {
                                                                echo "<option value='{$week['week_id']}'>Week {$week['week_number']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Vendor</label>
                                                        <select name="vendor_id" class="form-select" id="deleteVendorSelect" required>
                                                            <option value="">-- Select Vendor --</option>
                                                            <?php
                                                            mysqli_data_seek($vendorResult, 0);
                                                            while($vendor = mysqli_fetch_assoc($vendorResult)) {
                                                                echo "<option value='{$vendor['id']}'>{$vendor['name']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Select Menu</label>
                                                <select name="id" class="form-select" id="menuSelect" required>
                                                    <option value="">-- Select Week & Vendor First --</option>
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-danger">Delete Menu</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Menu Existing -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Existing Menus</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Week</th>
                                                        <th>Vendor</th>
                                                        <th>Day</th>
                                                        <th>Menu Name</th>
                                                        <th>Keterangan</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $menuQuery = "SELECT m.*, w.week_number, nv.name as vendor_name 
                                                                FROM menu m 
                                                                JOIN week w ON m.week_id = w.id 
                                                                JOIN nama_vendor nv ON m.vendor_id = nv.id 
                                                                ORDER BY w.week_number, nv.name, 
                                                                FIELD(m.day, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')";
                                                    $menuResult = mysqli_query($conn, $menuQuery);
                                                    
                                                    if(mysqli_num_rows($menuResult) > 0) {
                                                        while($menu = mysqli_fetch_assoc($menuResult)) {
                                                            echo "<tr>
                                                                    <td>Week {$menu['week_number']}</td>
                                                                    <td>{$menu['vendor_name']}</td>
                                                                    <td>{$menu['day']}</td>
                                                                    <td>{$menu['menu_name']}</td>
                                                                    <td>{$menu['keterangan']}</td>
                                                                    <td class='action-buttons'>
                                                                        <form method='GET' action='execution/menu_action.php' style='display: inline;'>
                                                                            <input type='hidden' name='delete' value='{$menu['id']}'>
                                                                            <button type='submit' class='btn btn-sm btn-danger' 
                                                                                    onclick='return confirm(\"Yakin ingin menghapus menu ini?\")'>
                                                                                <i class='bx bx-trash'></i> Hapus
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='6' class='text-center'>No menus found</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
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
    <script src="../assets/js/main.js"></script>

    <!-- Menu selection script -->
    <script>
    function loadMenus() {
        const weekId = document.getElementById('deleteWeekSelect').value;
        const vendorId = document.getElementById('deleteVendorSelect').value;
        const menuSelect = document.getElementById('menuSelect');
        
        // Reset menu select
        menuSelect.innerHTML = '<option value="">-- Select Menu --</option>';
        
        if (weekId && vendorId) {
            // Fetch menus for selected week and vendor
            fetch('action/get_menus.php?week_id=' + weekId + '&vendor_id=' + vendorId)
                .then(response => response.json())
                .then(menus => {
                    if (menus.length > 0) {
                        menus.forEach(menu => {
                            const option = document.createElement('option');
                            option.value = menu.id;
                            option.textContent = `${menu.day} - ${menu.menu_name}`;
                            menuSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No menus found for this week and vendor';
                        menuSelect.appendChild(option);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }

    document.getElementById('deleteWeekSelect').addEventListener('change', loadMenus);
    document.getElementById('deleteVendorSelect').addEventListener('change', loadMenus);
    </script>
</body>
</html>