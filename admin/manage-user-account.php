<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; 
// Ambil data department aktif
$query = "SELECT id, name FROM department WHERE is_active = 1 ORDER BY name ASC";
$result = mysqli_query($conn, $query);
// NOTE: user list will be fetched via AJAX endpoint (see config/search_users.php)
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

    <title>Manage User Account | ParaCanteen</title>

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
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php include_once __DIR__ . '/layout/sidebar.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <?php include_once __DIR__ . '/layout/navbar.php'; ?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Manage User Account</h4>

              <!-- Basic Layout -->
              <div class="row">
              <!-- Form Tambah Akun -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New User Account</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/user_action.php" method="POST">
                      <input type="hidden" name="action" value="add">

                      <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="nama" class="form-control" placeholder="Full Name" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="departement" class="form-select" required>
                          <option value="">-- Pilih Department --</option>
                          <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                          type="email"
                          name="gmail"
                          class="form-control"
                          placeholder="kantin1@example.com"
                          required
                          pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                          title="Masukkan alamat email yang valid, misalnya: nama@example.com"
                        >
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                      </div>

                      <button type="submit" class="btn btn-primary">Add Account</button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Form Hapus Akun -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Delete User Account</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/user_action.php" method="POST">
                      <input type="hidden" name="action" value="delete">

                      <div class="mb-3">
                        <label class="form-label">Select Account</label>
                        <!-- Searchable input + hidden id -->
                        <div class="position-relative">
                          <input type="text" id="userSearch" class="form-control" placeholder="Search user by name or email" aria-label="Search user">
                          <input type="hidden" name="id" id="selectedUserId" required>
                          <div id="userList" class="list-group position-absolute w-100" style="z-index:1050; display:none; max-height:220px; overflow:auto;"></div>
                        </div>
                        <small class="text-muted">Type to search and pick a user. Click an item to select.</small>
                        <style>
                          /* Make dropdown opaque and readable regardless of page background */
                          #userList {
                            z-index: 1050; /* ensure above other UI */
                            background: #ffffff;
                            border: 1px solid rgba(0,0,0,0.08);
                            border-radius: 0.375rem;
                            box-shadow: 0 0.5rem 1rem rgba(22,28,45,0.06);
                          }
                          #userList .list-group-item {
                            cursor: pointer;
                            background: transparent;
                            color: #212529;
                          }
                          #userList .list-group-item:hover,
                          #userList .list-group-item.focus {
                            background-color: #f1f3f5;
                            color: #212529;
                          }
                          #userList .list-group-item.active {
                            background-color: #e9ecef;
                            color: #212529;
                          }
                        </style>
                        <script>
                          (function(){
                            const input = document.getElementById('userSearch');
                            const list = document.getElementById('userList');
                            const hidden = document.getElementById('selectedUserId');
                            let currentItems = [];
                            const CACHE_KEY = 'userSearchCache_v1';

                            function loadCache() {
                              try { return JSON.parse(localStorage.getItem(CACHE_KEY) || '{}'); } catch (e) { return {}; }
                            }
                            function saveCache(c) { try { localStorage.setItem(CACHE_KEY, JSON.stringify(c)); } catch (e) {} }

                            // last-selected persistence removed per request

                            function debounce(fn, wait) {
                              let t;
                              return function() { clearTimeout(t); t = setTimeout(() => fn.apply(this, arguments), wait); };
                            }

                            async function fetchUsers(q) {
                              if (!q || q.length < 1) { list.style.display = 'none'; return; }
                              const cache = loadCache();
                              if (cache[q]) {
                                currentItems = cache[q];
                                renderItems(currentItems);
                                return;
                              }
                              try {
                                const res = await fetch('./config/search_users.php?q=' + encodeURIComponent(q));
                                if (!res.ok) throw new Error('Network error');
                                const data = await res.json();
                                currentItems = data;
                                cache[q] = data;
                                // keep cache small (LRU-ish): trim to last 20 keys
                                const keys = Object.keys(cache);
                                if (keys.length > 20) {
                                  const drop = keys.slice(0, keys.length - 20);
                                  drop.forEach(k => delete cache[k]);
                                }
                                saveCache(cache);
                                renderItems(data);
                              } catch (err) {
                                console.error(err);
                                list.style.display = 'none';
                              }
                            }

                            function renderItems(items) {
                              list.innerHTML = '';
                              if (!items || !items.length) { list.style.display = 'none'; return; }
                              items.forEach(u => {
                                const el = document.createElement('button');
                                el.type = 'button';
                                el.className = 'list-group-item list-group-item-action';
                                el.textContent = u.nama + ' (' + u.gmail + ')';
                                el.dataset.id = u.id;
                                el.addEventListener('click', function(){
                                  input.value = this.textContent;
                                  hidden.value = this.dataset.id;
                                  // do not persist last-selected
                                  list.style.display = 'none';
                                });
                                list.appendChild(el);
                              });
                              list.style.display = 'block';
                            }

                            // no prefill of last-selected per request

                            input.addEventListener('input', debounce(function(){
                              hidden.value = '';
                              fetchUsers(this.value.trim());
                            }, 250));

                            // hide on outside click
                            document.addEventListener('click', function(e){
                              if (!document.getElementById('userSearch').contains(e.target) && !document.getElementById('userList').contains(e.target)) {
                                list.style.display = 'none';
                              }
                            });

                            // keyboard navigation
                            input.addEventListener('keydown', function(e){
                              const items = Array.from(list.querySelectorAll('.list-group-item'));
                              if (!items.length) return;
                              const active = list.querySelector('.list-group-item.focus');
                              let idx = items.indexOf(active);
                              if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                idx = Math.min(items.length - 1, idx + 1);
                                if (active) active.classList.remove('focus');
                                items[idx].classList.add('focus');
                                items[idx].scrollIntoView({block:'nearest'});
                              } else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                idx = Math.max(0, idx - 1);
                                if (active) active.classList.remove('focus');
                                items[idx].classList.add('focus');
                                items[idx].scrollIntoView({block:'nearest'});
                              } else if (e.key === 'Enter') {
                                e.preventDefault();
                                const target = list.querySelector('.list-group-item.focus') || items[0];
                                if (target) { target.click(); }
                              }
                            });

                            // Remove a user from the cached results and last-selected
                            function removeUserFromCacheById(id) {
                              if (!id) return;
                              try {
                                const cache = loadCache();
                                let changed = false;
                                Object.keys(cache).forEach(k => {
                                  const arr = cache[k] || [];
                                  const filtered = arr.filter(u => String(u.id) !== String(id));
                                  if (filtered.length !== arr.length) {
                                    cache[k] = filtered;
                                    changed = true;
                                  }
                                });
                                if (changed) saveCache(cache);
                                // do not manage last-selected key
                              } catch (e) {
                                console.error('cache remove error', e);
                              }
                            }

                            // Hook delete form submit to purge cache for selected user
                            try {
                              const deleteForm = document.querySelector('form[action="execution/user_action.php"][method="POST"]');
                              if (deleteForm) {
                                deleteForm.addEventListener('submit', function(){
                                  const id = hidden.value;
                                  if (id) removeUserFromCacheById(id);
                                });
                              }
                            } catch (e) {
                              console.error(e);
                            }
                          })();
                        </script>
                      </div>

                      <button type="submit" class="btn btn-danger">Delete Account</button>
                    </form>
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
