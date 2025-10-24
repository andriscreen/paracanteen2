<!-- Bootstrap Icons CDN -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="index.php" class="app-brand-link">
              <span class="app-brand-logo demo">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M33.724 36.5809C37.7426 32.5622 40.0003 27.1118 40.0003 21.4286C40.0003 15.7454 37.7426 10.2949 33.724 6.27629C29.7054 2.25765 24.2549 1.02188e-06 18.5717 0C12.8885 -1.02188e-06 7.43807 2.25764 3.41943 6.27628L10.4905 13.3473C11.6063 14.4631 13.4081 14.4074 14.8276 13.7181C15.9836 13.1568 17.2622 12.8571 18.5717 12.8571C20.845 12.8571 23.0252 13.7602 24.6326 15.3677C26.2401 16.9751 27.1431 19.1553 27.1431 21.4286C27.1431 22.7381 26.8435 24.0167 26.2822 25.1727C25.5929 26.5922 25.5372 28.394 26.6529 29.5098L33.724 36.5809Z" fill="#696cff"></path>
                <path d="M30 40H19.5098C17.9943 40 16.5408 39.398 15.4692 38.3263L1.67368 24.5308C0.60204 23.4592 0 22.0057 0 20.4902V10L30 40Z" fill="#297AFF"></path>
                <path d="M10.7143 39.9999H4.28571C1.91878 39.9999 0 38.0812 0 35.7142V29.2856L10.7143 39.9999Z" fill="#34C2FF"></path>
                </svg>
              </span>
              <span class="app-brand-text demo menu-text fw-bolder ms-2">ParaCanteen</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item">
              <a href="index.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>

            <!-- Layouts -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bi bi-kanban"></i>
                <div data-i18n="Management Account">Management Account</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="manage-pic-account.php" class="menu-link">
                    <div data-i18n="PIC Account">PIC Account</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="manage-vendor-account.php" class="menu-link">
                    <div data-i18n="Vendor Account">Vendor Account</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="manage-user-account.php" class="menu-link">
                    <div data-i18n="User Account">User Account</div>
                  </a>
                </li>
              </ul>
            </li>

            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">billing page</span>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Bill">Bill</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div data-i18n="Tagihan Makan">Tagihan Makan</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div data-i18n="Tagihan Kupon">Tagihan Kupon</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Update data -->
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Update data</span>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bi bi-folder-plus"></i>
                <div data-i18n="Bill">Update</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="update-menu.php" class="menu-link">
                    <div data-i18n="Vendor Name">Update Menu</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="update-user.php" class="menu-link">
                    <div data-i18n="Vendor Name">Update User</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="update-vendor-name.php" class="menu-link">
                    <div data-i18n="Vendor Name">Vendor Name</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="update-departement-name.php" class="menu-link">
                    <div data-i18n="Department Name">Department Name</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="update-plant-name.php" class="menu-link">
                    <div data-i18n="Plant Name">Plant Name</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="update-place-name.php" class="menu-link">
                    <div data-i18n="Place Name">Place Name</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- / Check update data -->
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Check Again</span>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bi bi-folder-plus"></i>
                <div data-i18n="Bill">Check Update Data</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="check-update-user.php" class="menu-link">
                    <div data-i18n="Vendor Name">Check Update User</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="check-update-menu.php" class="menu-link">
                    <div data-i18n="Vendor Name">Check Update Menu</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="check-update-data.php" class="menu-link">
                    <div data-i18n="Vendor Name">Check Update Data</div>
                  </a>
                </li>
              </ul>
            </li>
            
            <!-- Components -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Components</span></li>
            <!-- Cards -->
            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Notifications</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="manage-user-order.php" class="menu-link">
                <i class="menu-icon bi bi-menu-button"></i>
                <div data-i18n="Basic">Control User Order</div>
              </a>
            </li>
            <!-- Misc -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
            <li class="menu-item">
              <a
                href="#"
                class="menu-link"
              >
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div data-i18n="Support">Support</div>
              </a>
            </li>
            <li class="menu-item">
              <a
                href="#"
                class="menu-link"
              >
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Documentation">Documentation</div>
              </a>
            </li>
          </ul>
        </aside>