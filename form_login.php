<?php include "redirect_if_logged_in.php"; ?>
<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Sign In | Paracanteen</title>

    <meta name="description" content="Sign in to Paracanteen to manage your account and orders." />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M33.724 36.5809C37.7426 32.5622 40.0003 27.1118 40.0003 21.4286C40.0003 15.7454 37.7426 10.2949 33.724 6.27629C29.7054 2.25765 24.2549 1.02188e-06 18.5717 0C12.8885 -1.02188e-06 7.43807 2.25764 3.41943 6.27628L10.4905 13.3473C11.6063 14.4631 13.4081 14.4074 14.8276 13.7181C15.9836 13.1568 17.2622 12.8571 18.5717 12.8571C20.845 12.8571 23.0252 13.7602 24.6326 15.3677C26.2401 16.9751 27.1431 19.1553 27.1431 21.4286C27.1431 22.7381 26.8435 24.0167 26.2822 25.1727C25.5929 26.5922 25.5372 28.394 26.6529 29.5098L33.724 36.5809Z" fill="#696cff"></path>
                    <path d="M30 40H19.5098C17.9943 40 16.5408 39.398 15.4692 38.3263L1.67368 24.5308C0.60204 23.4592 0 22.0057 0 20.4902V10L30 40Z" fill="#297AFF"></path>
                    <path d="M10.7143 39.9999H4.28571C1.91878 39.9999 0 38.0812 0 35.7142V29.2856L10.7143 39.9999Z" fill="#34C2FF"></path>
                    </svg>
                  </span>
                  <span class="app-brand-text demo text-body fw-bolder">Paracanteen</span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Welcome Paragonan! 👋</h4>
              <p class="mb-4">Please sign-in to your account and start the adventure</p>

              <form id="formAuthentication" class="mb-3" action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="">
                
                <div class="mb-3">
                  <label for="gmail" class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="gmail"
                    name="gmail"
                    placeholder="Enter your email"
                    required
                    autofocus
                  />
                </div>
                
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                    <a href="#">
                      <small>Forgot Password?</small>
                    </a>
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="••••••••••••"
                      autocomplete="current-password"
                      required
                    />
                    <button class="input-group-text cursor-pointer" type="button" aria-label="Toggle password visibility">
                      <i class="bx bx-hide" aria-hidden="true"></i>
                    </button>
                  </div>
                </div>
                
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" value="1" />
                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                  </div>
                </div>
                
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                </div>
              </form>


              <p class="text-center">
                <span>New on our platform?</span>
                <a href="form_register.php">
                  <span>Create an account</span>
                </a>
              </p>
              <p>sahroni@example.com | password123
                vendor@example.com | password123
                admin@example.com | password123</p>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->

    <script>
      (function () {
        var btn = document.querySelector('.form-password-toggle .input-group-text.cursor-pointer');
        if (!btn) return;
        var input = document.getElementById('password');
        var icon = btn.querySelector('i');
        function toggle() {
          if (!input) return;
          var isPw = input.type === 'password';
          input.type = isPw ? 'text' : 'password';
          if (icon) {
            icon.classList.toggle('bx-hide', !isPw ? true : false);
            icon.classList.toggle('bx-show', isPw ? true : false);
          }
        }
        btn.addEventListener('click', toggle);
      })();
    </script>
  </body>
</html>
