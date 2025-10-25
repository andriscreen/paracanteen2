<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendorkantin') {
    header("Location: ../form_login.php");
    exit;
}

include 'config/db.php';

// Set locale untuk bahasa Indonesia
$conn->query("SET lc_time_names = 'id_ID'");

// Ambil hari ini
$hari_ini_result = $conn->query("SELECT DAYNAME(CURDATE()) as hari_ini");
$hari_ini_row = $hari_ini_result->fetch_assoc();
$hari_ini = $hari_ini_row['hari_ini'];

// Konversi ke format Indonesia
$hari_indonesia = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$hari_ini_indonesia = $hari_indonesia[$hari_ini] ?? $hari_ini;

// Ambil week dari order terbaru
$current_week_result = $conn->query("
    SELECT o.week_id as id, w.week_number 
    FROM orders o 
    JOIN week w ON o.week_id = w.id 
    ORDER BY o.created_at DESC 
    LIMIT 1
");

$current_week = $current_week_result->fetch_assoc();
$current_week_id = $current_week['id'] ?? 0;
$current_week_number = $current_week['week_number'] ?? 0;

// Variabel untuk response
$response = ['status' => '', 'message' => '', 'user_data' => null, 'icon' => ''];

// Proses validasi RFID jika ada request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rfid'])) {
    $rfid = trim($_POST['rfid']);
    
    if (empty($rfid)) {
        $response = [
            'status' => 'error', 
            'message' => 'RFID tidak boleh kosong',
            'icon' => 'bx-x-circle'
        ];
    } else {
        // Cari user berdasarkan RFID
        $stmt = $conn->prepare("
            SELECT u.id, u.nama, u.nip, u.departemen, u.rfid 
            FROM users u 
            WHERE u.rfid = ?
        ");
        $stmt->bind_param("s", $rfid);
        $stmt->execute();
        $user_result = $stmt->get_result();
        
        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            
            // Cek order untuk week ini
            $order_stmt = $conn->prepare("
                SELECT o.id as order_id, o.week_id,
                o.makan_senin, o.makan_selasa, o.makan_rabu, o.makan_kamis,
                o.makan_jumat, o.makan_sabtu, o.makan_minggu
                FROM orders o 
                WHERE o.user_id = ? AND o.week_id = ?
                ORDER BY o.id DESC 
                LIMIT 1
            ");
            $order_stmt->bind_param("ii", $user['id'], $current_week_id);
            $order_stmt->execute();
            $order_result = $order_stmt->get_result();
            
            $boleh_makan = false;
            $order_data = null;
            
            if ($order_result->num_rows > 0) {
                $order_data = $order_result->fetch_assoc();
                
                // Cek boleh makan hari ini
                switch ($hari_ini_indonesia) {
                    case 'Senin': $boleh_makan = $order_data['makan_senin'] == 1; break;
                    case 'Selasa': $boleh_makan = $order_data['makan_selasa'] == 1; break;
                    case 'Rabu': $boleh_makan = $order_data['makan_rabu'] == 1; break;
                    case 'Kamis': $boleh_makan = $order_data['makan_kamis'] == 1; break;
                    case 'Jumat': $boleh_makan = $order_data['makan_jumat'] == 1; break;
                    case 'Sabtu': $boleh_makan = $order_data['makan_sabtu'] == 1; break;
                    case 'Minggu': $boleh_makan = $order_data['makan_minggu'] == 1; break;
                }
            }
            
            if ($boleh_makan) {
                // Cek sudah validasi hari ini
                $validation_stmt = $conn->prepare("
                    SELECT id, validation_time 
                    FROM meal_validations 
                    WHERE user_id = ? AND validation_date = CURDATE() AND status = 'validated'
                ");
                $validation_stmt->bind_param("i", $user['id']);
                $validation_stmt->execute();
                $validation_result = $validation_stmt->get_result();
                
                if ($validation_result->num_rows > 0) {
                    $validation_data = $validation_result->fetch_assoc();
                    $response = [
                        'status' => 'warning', 
                        'message' => $user['nama'] . ' sudah makan hari ini<br><small>Jam: ' . $validation_data['validation_time'] . '</small>',
                        'user_data' => $user,
                        'icon' => 'bx-error-circle'
                    ];
                } else {
                    // Simpan validasi
                    $insert_stmt = $conn->prepare("
                        INSERT INTO meal_validations 
                        (user_id, rfid, order_id, week_id, day, meal_type, validation_date, validation_time) 
                        VALUES (?, ?, ?, ?, ?, 'lunch', CURDATE(), CURTIME())
                    ");
                    $insert_stmt->bind_param(
                        "siiis", 
                        $user['id'], 
                        $rfid, 
                        $order_data['order_id'], 
                        $current_week_id,
                        $hari_ini_indonesia
                    );
                    
                    if ($insert_stmt->execute()) {
                        $response = [
                            'status' => 'success', 
                            'message' => 'Selamat makan!<br><strong>' . $user['nama'] . '</strong><br><small>Week ' . $current_week_number . ' - ' . $hari_ini_indonesia . '</small>',
                            'user_data' => $user,
                            'icon' => 'bx-check-circle'
                        ];
                    } else {
                        $response = [
                            'status' => 'error', 
                            'message' => 'Gagal menyimpan validasi',
                            'user_data' => $user,
                            'icon' => 'bx-x-circle'
                        ];
                    }
                }
            } else {
                $response = [
                    'status' => 'error', 
                    'message' => $user['nama'] . ' tidak ada jadwal makan<br>hari ' . $hari_ini_indonesia . ' - Week ' . $current_week_number,
                    'user_data' => $user,
                    'icon' => 'bx-calendar-x'
                ];
            }
        } else {
            $response = [
                'status' => 'error', 
                'message' => 'RFID tidak terdaftar',
                'icon' => 'bx-user-x'
            ];
        }
    }
    
    // Jika request AJAX, kembalikan JSON
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Ambil riwayat validasi hari ini
$today_validations = $conn->query("
    SELECT mv.*, u.nama, u.nip, u.departemen
    FROM meal_validations mv 
    JOIN users u ON mv.user_id = u.id 
    WHERE mv.validation_date = CURDATE() 
    ORDER BY mv.validation_time DESC 
    LIMIT 10
");
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
    <title>Validasi Makan RFID - ParaCanteen</title>

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
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

    <style>
        .scanner-container {
            text-align: center;
            padding: 40px 20px;
        }
        .scanner-icon {
            font-size: 80px;
            color: #696cff;
            margin-bottom: 20px;
        }
        .rfid-input {
            font-size: 24px;
            text-align: center;
            letter-spacing: 3px;
            padding: 20px;
            border: 3px dashed #ddd;
            border-radius: 15px;
            margin: 30px 0;
            transition: all 0.3s;
            width: 100%;
            max-width: 500px;
        }
        .rfid-input:focus {
            border-color: #696cff;
            box-shadow: 0 0 20px rgba(105, 108, 255, 0.3);
            outline: none;
        }
        .btn-scan {
            background: #696cff;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }
        .btn-scan:hover {
            background: #5f61e6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .system-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid #696cff;
        }
        .history-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }
        .history-item:hover {
            background: #f8f9fa;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #696cff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
        }
        .user-info {
            flex: 1;
        }
        .user-name {
            font-weight: bold;
            color: #333;
        }
        .user-details {
            font-size: 12px;
            color: #666;
        }
        .validation-time {
            color: #28a745;
            font-weight: bold;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: modalAppear 0.5s ease-out;
        }
        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-50px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .modal-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .modal-success .modal-icon {
            color: #28a745;
        }
        .modal-error .modal-icon {
            color: #dc3545;
        }
        .modal-warning .modal-icon {
            color: #ffc107;
        }
        .modal-message {
            font-size: 18px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .week-info-card {
            background: linear-gradient(135deg, #696cff 0%, #696cff 100%);
            color: white !important;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .week-info-card h4,
        .week-info-card h2 {
            color: white !important;
            margin-bottom: 0;
        }
    </style>
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
              <div class="row">
                <div class="col-lg-8">
                  <div class="card mb-4">
                    <div class="card-header">
                      <h4 class="mb-0"><i class="bx bx-qr-scan"></i> Validasi Makan RFID</h4>
                    </div>
                    <div class="card-body">
                      <div class="scanner-container">
                        <div class="scanner-icon">
                          <i class='bx bx-credit-card-front'></i>
                        </div>
                        <h3>Tempelkan Kartu RFID</h3>
                        <p class="text-muted">Scan kartu karyawan untuk validasi makan siang</p>
                        
                        <form id="rfidForm" method="POST">
                          <input type="text" 
                                 id="rfid" 
                                 name="rfid" 
                                 class="rfid-input" 
                                 placeholder="Scan RFID..." 
                                 required 
                                 autofocus
                                 autocomplete="off"
                                 maxlength="20">
                          
                          <br>
                          <button type="submit" class="btn-scan">
                            <i class='bx bx-check-shield'></i> PROSES VALIDASI
                          </button>
                        </form>
                        
                        <div class="system-info">
                          <i class='bx bx-info-circle'></i> 
                          Sistem siap menerima scan RFID. Pastikan reader terhubung.
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-4">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="mb-0"><i class="bx bx-calendar"></i> Informasi Sistem</h5>
                    </div>
                    <div class="card-body">
                      <div class="week-info-card">
                        <h4 class="mb-2"><?= $hari_ini_indonesia ?></h4>
                        <h2 class="mb-0">Week <?= $current_week_number ?></h2>
                      </div>
                      
                      <h6 class="mt-4"><i class="bx bx-history"></i> Riwayat Validasi Hari Ini</h6>
                      <div style="max-height: 400px; overflow-y: auto;">
                        <?php if ($today_validations->num_rows > 0): ?>
                          <?php while($validation = $today_validations->fetch_assoc()): ?>
                            <div class="history-item">
                              <div class="user-avatar">
                                <?= strtoupper(substr($validation['nama'], 0, 2)) ?>
                              </div>
                              <div class="user-info">
                                <div class="user-name"><?= htmlspecialchars($validation['nama']) ?></div>
                                <div class="user-details">
                                  <?= $validation['nip'] ?> • <?= $validation['departemen'] ?>
                                </div>
                              </div>
                              <div class="validation-time">
                                <?= date('H:i', strtotime($validation['validation_time'])) ?>
                              </div>
                            </div>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <div class="text-center text-muted py-4">
                            <i class='bx bx-time' style="font-size: 48px;"></i>
                            <p class="mt-2">Belum ada validasi hari ini</p>
                          </div>
                        <?php endif; ?>
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
                  © <script>document.write(new Date().getFullYear());</script>,
                  Part of <a href="#" class="footer-link fw-bolder">ParagonCorp</a>
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

    <!-- Modal Popup -->
    <div id="resultModal" class="modal">
      <div class="modal-content" id="modalContent">
        <div class="modal-icon" id="modalIcon">
          <i class='bx bx-check-circle'></i>
        </div>
        <div class="modal-message" id="modalMessage">
          <!-- Message will be inserted here -->
        </div>
        <button class="btn btn-primary mt-3" onclick="closeModal()">
          <i class='bx bx-x'></i> TUTUP
        </button>
      </div>
    </div>

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
        let autoCloseTimer;
        
        function showModal(status, message, icon = '') {
            const modal = document.getElementById('resultModal');
            const modalContent = document.getElementById('modalContent');
            const modalIcon = document.getElementById('modalIcon');
            const modalMessage = document.getElementById('modalMessage');
            
            // Set modal class based on status
            modalContent.className = 'modal-content';
            modalContent.classList.add('modal-' + status);
            
            // Set icon
            modalIcon.innerHTML = `<i class='bx ${icon}'></i>`;
            
            // Set message
            modalMessage.innerHTML = message;
            
            // Show modal
            modal.style.display = 'flex';
            
            // Auto close after 3 seconds for success
            if (status === 'success') {
                clearTimeout(autoCloseTimer);
                autoCloseTimer = setTimeout(closeModal, 6000);
            }
        }
        
        function closeModal() {
            document.getElementById('resultModal').style.display = 'none';
            // Clear input and focus
            document.getElementById('rfid').value = '';
            document.getElementById('rfid').focus();
        }
        
        $(document).ready(function() {
            // Auto focus ke input RFID
            $('#rfid').focus();
            
            // Auto submit ketika input RFID berubah (untuk RFID reader)
            $('#rfid').on('input', function() {
                if ($(this).val().length >= 8) {
                    setTimeout(() => {
                        $('#rfidForm').submit();
                    }, 500);
                }
            });
            
            // Form submission dengan AJAX
            $('#rfidForm').on('submit', function(e) {
                e.preventDefault();
                const rfid = $('#rfid').val().trim();
                
                if (rfid.length > 0) {
                    // Show loading state
                    $('.btn-scan').html('<i class="bx bx-loader-alt bx-spin"></i> MEMPROSES...');
                    $('.btn-scan').prop('disabled', true);
                    
                    $.ajax({
                        url: 'validasi-user-makan.php',
                        type: 'POST',
                        data: {
                            rfid: rfid,
                            ajax: true
                        },
                        success: function(response) {
                            // Show modal with result
                            showModal(response.status, response.message, response.icon);
                            
                            // Reload history after a short delay
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        },
                        error: function() {
                            showModal('error', 'Terjadi kesalahan sistem', 'bx-error');
                        },
                        complete: function() {
                            // Reset button state
                            $('.btn-scan').html('<i class="bx bx-check-shield"></i> PROSES VALIDASI');
                            $('.btn-scan').prop('disabled', false);
                        }
                    });
                }
            });
            
            // Close modal on background click
            $('#resultModal').on('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
            
            // Close modal with ESC key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>
  </body>
</html>

<?php
// Tutup koneksi
if (isset($conn) && $conn) {
    $conn->close();
}
?>