<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'pickantin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; ?>

<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Order ID tidak valid!";
    header("Location: manage-user-order.php");
    exit;
}

$order_id = (int)$_GET['id'];

// Query untuk mendapatkan detail order
$query = "SELECT o.*, u.nama as user_nama, u.nip, u.departemen, u.gmail, u.rfid, 
                 w.week_number, y.year_value, p.name as plant_name, 
                 pl.name as place_name, s.nama_shift
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          LEFT JOIN week w ON o.week_id = w.id 
          LEFT JOIN year y ON o.year_id = y.id 
          LEFT JOIN plant p ON o.plant_id = p.id 
          LEFT JOIN place pl ON o.place_id = pl.id 
          LEFT JOIN shift s ON o.shift_id = s.id 
          WHERE o.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order tidak ditemukan!";
    header("Location: manage-user-order.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    
    try {
        // Update data dasar
        $week_id = $_POST['week_id'];
        $year_id = $_POST['year_id'];
        $plant_id = $_POST['plant_id'];
        $place_id = $_POST['place_id'];
        $shift_id = $_POST['shift_id'];
        
        // Update hari - PERBAIKAN: Gunakan radio button values
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $update_fields = [];
        $update_values = [];
        
        foreach ($days as $day) {
            $day_choice = $_POST["day_$day"] ?? 'libur'; // default ke libur jika tidak dipilih
            
            $makan_value = ($day_choice === 'makan') ? 1 : 0;
            $kupon_value = ($day_choice === 'kupon') ? 1 : 0;
            $libur_value = ($day_choice === 'libur') ? 1 : 0;
            
            // Validasi: hanya satu yang bisa dipilih (sekarang sudah pasti benar)
            if ($makan_value + $kupon_value + $libur_value != 1) {
                throw new Exception("Hari " . ucfirst($day) . " hanya boleh memilih satu opsi!");
            }
            
            $update_fields[] = "makan_$day = ?";
            $update_fields[] = "kupon_$day = ?";
            $update_fields[] = "libur_$day = ?";
            
            $update_values[] = $makan_value;
            $update_values[] = $kupon_value;
            $update_values[] = $libur_value;
        }
        
        // Build update query
        $update_query = "UPDATE orders SET 
                        week_id = ?, year_id = ?, plant_id = ?, place_id = ?, shift_id = ?, " .
                        implode(", ", $update_fields) . " WHERE id = ?";
        
        $stmt = $conn->prepare($update_query);
        
        // Bind parameters
        $params = array_merge([$week_id, $year_id, $plant_id, $place_id, $shift_id], $update_values, [$order_id]);
        $types = str_repeat('i', count($params));
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
        
        // Update kupon history jika ada perubahan kupon
        $old_kupon_total = 0;
        $new_kupon_total = 0;
        
        foreach ($days as $day) {
            $old_kupon_total += $order["kupon_$day"];
            $new_kupon_total += ($_POST["day_$day"] ?? '') === 'kupon' ? 1 : 0;
        }
        
        // Update kupon history dan user kupon
        if ($old_kupon_total != $new_kupon_total) {
            $kupon_diff = $new_kupon_total - $old_kupon_total;
            
            // Update atau insert kupon_history
            $check_kupon = $conn->prepare("SELECT id FROM kupon_history WHERE order_id = ?");
            $check_kupon->bind_param("i", $order_id);
            $check_kupon->execute();
            $kupon_exists = $check_kupon->get_result()->fetch_assoc();
            
            if ($kupon_exists) {
                // Update existing
                $update_kupon = $conn->prepare("UPDATE kupon_history SET jumlah_kupon = ? WHERE order_id = ?");
                $update_kupon->bind_param("ii", $new_kupon_total, $order_id);
                $update_kupon->execute();
            } else if ($new_kupon_total > 0) {
                // Insert new
                $insert_kupon = $conn->prepare("INSERT INTO kupon_history (user_id, order_id, jumlah_kupon, keterangan) VALUES (?, ?, ?, ?)");
                $keterangan = "Kupon dari pemesanan makanan";
                $insert_kupon->bind_param("iiis", $order['user_id'], $order_id, $new_kupon_total, $keterangan);
                $insert_kupon->execute();
            }
            
            // Update total kupon user
            $update_user = $conn->prepare("UPDATE users SET total_kupon = GREATEST(0, total_kupon + ?) WHERE id = ?");
            $update_user->bind_param("ii", $kupon_diff, $order['user_id']);
            $update_user->execute();
        }
        
        $conn->commit();
        $_SESSION['success'] = "Order berhasil diupdate!";
        header("Location: view_order.php?id=" . $order_id);
        exit;
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Gagal mengupdate order: " . $e->getMessage();
        
        // Debug info
        error_log("Order update error: " . $e->getMessage());
        error_log("POST data: " . print_r($_POST, true));
    }
}

// Get data untuk dropdown
$weeks = $conn->query("SELECT * FROM week ORDER BY week_number");
$years = $conn->query("SELECT * FROM year ORDER BY year_value");
$plants = $conn->query("SELECT * FROM plant ORDER BY name");
$shifts = $conn->query("SELECT * FROM shift ORDER BY id");

// Get places berdasarkan plant_id yang dipilih
$places = $conn->query("SELECT * FROM place WHERE plant_id = " . $order['plant_id'] . " ORDER BY name");
?>

<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Edit Order - ParaCanteen</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <style>
        .day-option { cursor: pointer; transition: all 0.3s ease; }
        .day-option:hover { transform: translateY(-2px); }
        .day-option.selected { border: 2px solid #007bff; }
        .option-makan.selected { background-color: #d4edda !important; }
        .option-kupon.selected { background-color: #fff3cd !important; }
        .option-libur.selected { background-color: #e2e3e5 !important; }
    </style>
</head>
<body>
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
                            <div class="col-lg-12 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0">
                                            <i class="bx bx-edit"></i> Edit Order #<?= $order['id'] ?>
                                        </h4>
                                        <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <!-- Alert messages -->
                                        <?php if (isset($_SESSION['error'])): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php endif; ?>

                                        <form method="POST" id="editOrderForm">
                                            <!-- Informasi User (Readonly) -->
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0"><i class="bx bx-user"></i> Informasi User</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <strong>Nama:</strong> <?= htmlspecialchars($order['user_nama']) ?>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>NIP:</strong> <?= htmlspecialchars($order['nip']) ?>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>Departemen:</strong> 
                                                                    <?php if (!empty($order['departemen'])): ?>
                                                                        <span class="badge bg-primary"><?= htmlspecialchars($order['departemen']) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">-</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Informasi Order -->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label">Minggu <span class="text-danger">*</span></label>
                                                    <select name="week_id" class="form-select" required>
                                                        <?php while ($week = $weeks->fetch_assoc()): ?>
                                                            <option value="<?= $week['id'] ?>" <?= $week['id'] == $order['week_id'] ? 'selected' : '' ?>>
                                                                Minggu <?= $week['week_number'] ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                                    <select name="year_id" class="form-select" required>
                                                        <?php while ($year = $years->fetch_assoc()): ?>
                                                            <option value="<?= $year['id'] ?>" <?= $year['id'] == $order['year_id'] ? 'selected' : '' ?>>
                                                                <?= $year['year_value'] ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <label class="form-label">Plant <span class="text-danger">*</span></label>
                                                    <select name="plant_id" class="form-select" id="plantSelect" required>
                                                        <?php while ($plant = $plants->fetch_assoc()): ?>
                                                            <option value="<?= $plant['id'] ?>" <?= $plant['id'] == $order['plant_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($plant['name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Place <span class="text-danger">*</span></label>
                                                    <select name="place_id" class="form-select" id="placeSelect" required>
                                                        <?php while ($place = $places->fetch_assoc()): ?>
                                                            <option value="<?= $place['id'] ?>" <?= $place['id'] == $order['place_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($place['name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Shift</label>
                                                    <select name="shift_id" class="form-select">
                                                        <option value="">Pilih Shift</option>
                                                        <?php while ($shift = $shifts->fetch_assoc()): ?>
                                                            <option value="<?= $shift['id'] ?>" <?= $shift['id'] == $order['shift_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($shift['nama_shift']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Pilihan Hari -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0"><i class="bx bx-calendar"></i> Pilihan Hari</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <?php 
                                                        $days_indonesia = [
                                                            'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 
                                                            'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 
                                                            'minggu' => 'Minggu'
                                                        ];
                                                        
                                                        foreach ($days_indonesia as $day_en => $day_id): 
                                                            $current_makan = $order["makan_$day_en"];
                                                            $current_kupon = $order["kupon_$day_en"];
                                                            $current_libur = $order["libur_$day_en"];
                                                        ?>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <strong><?= $day_id ?></strong>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input day-radio" type="radio" 
                                                                               name="day_<?= $day_en ?>" id="makan_<?= $day_en ?>" 
                                                                               value="makan" <?= $current_makan ? 'checked' : '' ?>>
                                                                        <label class="form-check-label w-100 day-option option-makan p-2 rounded <?= $current_makan ? 'selected' : '' ?>" 
                                                                               for="makan_<?= $day_en ?>">
                                                                            <i class="bx bx-restaurant"></i> Makan di Kantin
                                                                        </label>
                                                                    </div>
                                                                    
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input day-radio" type="radio" 
                                                                               name="day_<?= $day_en ?>" id="kupon_<?= $day_en ?>" 
                                                                               value="kupon" <?= $current_kupon ? 'checked' : '' ?>>
                                                                        <label class="form-check-label w-100 day-option option-kupon p-2 rounded <?= $current_kupon ? 'selected' : '' ?>" 
                                                                               for="kupon_<?= $day_en ?>">
                                                                            <i class="bx bx-gift"></i> Ambil Kupon
                                                                        </label>
                                                                    </div>
                                                                    
                                                                    <div class="form-check">
                                                                        <input class="form-check-input day-radio" type="radio" 
                                                                               name="day_<?= $day_en ?>" id="libur_<?= $day_en ?>" 
                                                                               value="libur" <?= $current_libur ? 'checked' : '' ?>>
                                                                        <label class="form-check-label w-100 day-option option-libur p-2 rounded <?= $current_libur ? 'selected' : '' ?>" 
                                                                               for="libur_<?= $day_en ?>">
                                                                            <i class="bx bx-home"></i> Libur
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tombol Aksi -->
                                            <div class="row">
                                                <div class="col-12 text-center">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bx bx-save"></i> Simpan Perubahan
                                                    </button>
                                                    <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-secondary">
                                                        <i class="bx bx-x"></i> Batal
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
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

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
        // Handle day selection - sederhanakan
        document.querySelectorAll('.day-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const card = this.closest('.card-body');
                card.querySelectorAll('.day-option').forEach(option => {
                    option.classList.remove('selected');
                });
                this.nextElementSibling.classList.add('selected');
            });
        });

        // Inisialisasi selection state pada load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.day-radio:checked').forEach(radio => {
                radio.nextElementSibling.classList.add('selected');
            });
        });

        // Handle place selection based on plant
        document.getElementById('plantSelect').addEventListener('change', function() {
            const plantId = this.value;
            
            fetch(`get_places.php?plant_id=${plantId}`)
                .then(response => response.json())
                .then(places => {
                    const placeSelect = document.getElementById('placeSelect');
                    placeSelect.innerHTML = '';
                    
                    places.forEach(place => {
                        const option = document.createElement('option');
                        option.value = place.id;
                        option.textContent = place.name;
                        placeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        });

        // Form validation
        document.getElementById('editOrderForm').addEventListener('submit', function(e) {
            let hasSelection = false;
            const days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            
            days.forEach(day => {
                if (document.querySelector(`input[name="day_${day}"]:checked`)) {
                    hasSelection = true;
                }
            });
            
            if (!hasSelection) {
                e.preventDefault();
                alert('Pilih setidaknya satu hari!');
                return false;
            }
        });
    </script>
</body>
</html>