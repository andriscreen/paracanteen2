<?php
require_once "../auth.php";
require_once "config/db.php";

if ($_SESSION['role'] !== 'user') {
    header("Location: ../form_login.php");
    exit;
}

// Get user's kupon balance
$user_id = $_SESSION['user_id'];
$query = "SELECT total_kupon FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$total_kupon_user = $user_data['total_kupon'] ?? 0;

// Get redeem items
$query_items = "SELECT * FROM redeem_items WHERE aktif = 1 ORDER BY kupon ASC";
$result_items = $conn->query($query_items);
$items = $result_items->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Tukar Kupon - ParaCanteen</title>

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

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!-- Config -->
    <script src="../assets/js/config.js"></script>

    <style>
      .menu-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
      }

      .menu-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e3e6f0;
      }

      .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      }

      .menu-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-bottom: 1px solid #e3e6f0;
      }

      .menu-card-body {
        padding: 1.25rem;
      }

      .menu-card-body h5 {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #566a7f;
      }

      .menu-card-body p {
        color: #697a8d;
        margin-bottom: 1rem;
      }

      .kupon-badge {
        background: linear-gradient(135deg, #ffd700, #ffb300);
        color: #000;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
      }

      .qty-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #d9dee3;
        border-radius: 6px;
        text-align: center;
        margin-bottom: 0.75rem;
      }

      .btn-detail {
        width: 100%;
        margin-bottom: 0.75rem;
      }

      .total-box {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        margin-top: 2rem;
      }

      .item-disabled {
        opacity: 0.6;
        position: relative;
      }

      .item-disabled::after {
        content: "Kupon Tidak Cukup";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
      }

      .card-balance {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
      }

      .card-balance .fs-4 {
        font-size: 2.5rem !important;
      }
      
      .confirmation-item {
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 0;
      }
      
      .confirmation-item:last-child {
        border-bottom: none;
      }
      
      .kupon-change {
        font-weight: 600;
        color: #ff6b6b;
      }
      
      .new-balance {
        font-weight: 600;
        color: #28a745;
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

              <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">User /</span> Penukaran Kupon
              </h4>

              <!-- Info kupon -->
              <div class="card card-balance mb-4">
                <div class="card-body text-center">
                  <h5 class="text-white mb-3">Saldo Kupon Anda</h5>
                  <p class="fs-4 fw-bold text-white mb-2">
                    💳 <?= number_format($total_kupon_user) ?> Kupon
                  </p>
                  <?php if ($total_kupon_user > 0): ?>
                    <small class="text-white-50">Kupon diperoleh dari pemesanan makan mingguan</small>
                  <?php else: ?>
                    <small class="text-warning">Anda belum memiliki kupon. Dapatkan kupon dengan memesan makanan mingguan.</small>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Daftar barang yang bisa ditukar -->
              <form id="redeemForm" method="POST" action="execution/process-redeem.php">
                <div class="menu-container">
                  <?php if (empty($items)): ?>
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body text-center py-5">
                          <i class="bx bx-package text-muted mb-3" style="font-size: 4rem;"></i>
                          <h5 class="text-muted">Tidak ada item penukaran yang tersedia</h5>
                          <p class="text-muted">Silakan coba lagi nanti</p>
                        </div>
                      </div>
                    </div>
                  <?php else: ?>
                    <?php foreach ($items as $item): ?>
                      <?php 
                        $is_disabled = $total_kupon_user < $item['kupon'];
                        $card_class = $is_disabled ? 'menu-card item-disabled' : 'menu-card';
                      ?>
                      <div class="<?= $card_class ?>" data-kupon="<?= $item['kupon'] ?>">
                        <img src="../<?= $item['gambar'] ?>" alt="<?= htmlspecialchars($item['nama']); ?>" onerror="this.src='../assets/img/placeholder.jpg'">
                        <div class="menu-card-body">
                          <h5><?= htmlspecialchars($item['nama']); ?></h5>
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="kupon-badge">
                              <i class="bx bx-coin me-1"></i><?= $item['kupon'] ?> Kupon
                            </span>
                          </div>
                          
                          <!-- Tombol Detail -->
                          <button type="button" class="btn btn-outline-info btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal<?= $item['id'] ?>">
                            <i class="bx bx-info-circle me-2"></i>Detail
                          </button>

                          <!-- Input Quantity -->
                          <input 
                            type="number" 
                            name="qty[<?= $item['id']; ?>]" 
                            class="qty-input" 
                            placeholder="Jumlah" 
                            min="0" 
                            max="10"
                            data-kupon="<?= $item['kupon']; ?>"
                            data-item-name="<?= htmlspecialchars($item['nama']) ?>"
                            oninput="updateTotal()"
                            <?= $is_disabled ? 'disabled' : '' ?>
                          >
                          <input type="hidden" name="item_name[<?= $item['id']; ?>]" value="<?= $item['nama']; ?>">
                          <input type="hidden" name="item_kupon[<?= $item['id']; ?>]" value="<?= $item['kupon']; ?>">
                        </div>
                      </div>

                      <!-- Modal Detail -->
                      <div class="modal fade" id="detailModal<?= $item['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Detail Item - <?= htmlspecialchars($item['nama']) ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="text-center mb-3">
                                <img src="../<?= $item['gambar'] ?>" alt="<?= htmlspecialchars($item['nama']); ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;" onerror="this.src='../assets/img/placeholder.jpg'">
                              </div>
                              <div class="mb-3">
                                <strong>Nama Item:</strong>
                                <p><?= htmlspecialchars($item['nama']) ?></p>
                              </div>
                              <div class="mb-3">
                                <strong>Harga:</strong>
                                <p><span class="badge bg-warning text-dark"><?= $item['kupon'] ?> Kupon</span></p>
                              </div>
                              <div class="mb-3">
                                <strong>Keterangan:</strong>
                                <p><?= !empty($item['keterangan']) ? htmlspecialchars($item['keterangan']) : 'Tidak ada keterangan tambahan' ?></p>
                              </div>
                              <div class="mb-3">
                                <strong>Status:</strong>
                                <p>
                                  <span class="badge <?= $item['aktif'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $item['aktif'] ? 'Tersedia' : 'Tidak Tersedia' ?>
                                  </span>
                                </p>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                              <?php if (!$is_disabled && $item['aktif']): ?>
                                <button type="button" class="btn btn-primary" onclick="focusQuantity(<?= $item['id'] ?>)">
                                  <i class="bx bx-cart me-2"></i>Tukar Sekarang
                                </button>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>

                <!-- Total kupon -->
                <div class="total-box">
                  <div class="row align-items-center">
                    <div class="col-md-6">
                      <h5 class="mb-2">Total Kupon yang Akan Ditukarkan:</h5>
                      <p class="fs-3 fw-bold text-primary mb-0" id="totalKupon">0 Kupon</p>
                    </div>
                    <div class="col-md-6 text-end">
                      <button type="button" class="btn btn-primary btn-lg" id="btnTukar" onclick="showConfirmation()" disabled>
                        <i class="bx bx-refresh me-2"></i>Tukar Sekarang
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <!-- /Content -->

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

    <!-- Modal Konfirmasi Penukaran -->
    <div class="modal fade" id="confirmationModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bx bx-check-shield me-2 text-primary"></i>Konfirmasi Penukaran Kupon
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info">
              <i class="bx bx-info-circle me-2"></i>
              <strong>Perhatian:</strong> Pastikan item yang dipilih sudah benar. Penukaran kupon tidak dapat dibatalkan setelah dikonfirmasi.
            </div>
            
            <div class="mb-4">
              <h6 class="mb-3">Detail Penukaran:</h6>
              <div id="confirmationItems">
                <!-- Items akan diisi oleh JavaScript -->
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="card border-0 bg-light">
                  <div class="card-body">
                    <h6 class="card-title">Saldo Saat Ini</h6>
                    <p class="fs-4 fw-bold text-primary mb-0"><?= number_format($total_kupon_user) ?> Kupon</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card border-0 bg-light">
                  <div class="card-body">
                    <h6 class="card-title">Total yang Akan Ditukar</h6>
                    <p class="fs-4 fw-bold text-warning mb-0" id="confirmationTotal">0 Kupon</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-4 text-center">
              <div class="alert alert-success">
                <i class="bx bx-coin me-2"></i>
                <strong>Saldo Setelah Penukaran:</strong>
                <span class="fs-5 fw-bold new-balance ms-2" id="newBalance"><?= number_format($total_kupon_user) ?> Kupon</span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="bx bx-x me-2"></i>Batal
            </button>
            <button type="button" class="btn btn-primary" onclick="submitRedeemForm()">
              <i class="bx bx-check me-2"></i>Ya, Tukar Sekarang
            </button>
          </div>
        </div>
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

    <!-- SweetAlert2 for nice popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        const totalKuponUser = <?= $total_kupon_user; ?>;

        function updateTotal() {
            let total = 0;
            const inputs = document.querySelectorAll('.qty-input:not(:disabled)');
            
            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                const kupon = parseInt(input.dataset.kupon);
                total += qty * kupon;
            });

            document.getElementById('totalKupon').textContent = total + ' Kupon';
            
            // Enable/disable tombol tukar
            const btnTukar = document.getElementById('btnTukar');
            if (total > 0 && total <= totalKuponUser) {
                btnTukar.disabled = false;
            } else {
                btnTukar.disabled = true;
            }
        }

        function focusQuantity(itemId) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal' + itemId));
            modal.hide();
            
            // Focus ke input quantity setelah modal tertutup
            setTimeout(() => {
                const input = document.querySelector(`input[name="qty[${itemId}]"]`);
                if (input) {
                    input.focus();
                    input.select();
                }
            }, 300);
        }

        function showConfirmation() {
            const total = parseInt(document.getElementById('totalKupon').textContent);
            
            if (total === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih item yang ingin ditukar!',
                    confirmButtonColor: '#696cff'
                });
                return;
            }

            if (total > totalKuponUser) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kupon Tidak Cukup',
                    text: 'Total kupon yang ditukar melebihi saldo Anda!',
                    confirmButtonColor: '#696cff'
                });
                return;
            }

            // Tampilkan detail items di modal konfirmasi
            const confirmationItems = document.getElementById('confirmationItems');
            confirmationItems.innerHTML = '';
            
            const inputs = document.querySelectorAll('.qty-input:not(:disabled)');
            let hasItems = false;
            
            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    hasItems = true;
                    const kupon = parseInt(input.dataset.kupon);
                    const itemName = input.dataset.itemName;
                    const totalKupon = qty * kupon;
                    
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'confirmation-item';
                    itemDiv.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${itemName}</strong>
                                <br>
                                <small class="text-muted">${qty} x ${kupon} kupon</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark">${totalKupon} Kupon</span>
                            </div>
                        </div>
                    `;
                    confirmationItems.appendChild(itemDiv);
                }
            });

            if (!hasItems) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih item yang ingin ditukar!',
                    confirmButtonColor: '#696cff'
                });
                return;
            }

            // Update total di modal konfirmasi
            document.getElementById('confirmationTotal').textContent = total + ' Kupon';
            document.getElementById('newBalance').textContent = (totalKuponUser - total) + ' Kupon';
            
            // Tampilkan modal konfirmasi
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            confirmationModal.show();
        }

        function submitRedeemForm() {
            // Tutup modal konfirmasi
            const confirmationModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            confirmationModal.hide();
            
            // Submit form
            document.getElementById('redeemForm').submit();
        }

        // Initialize total on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });
    </script>
  </body>
</html>