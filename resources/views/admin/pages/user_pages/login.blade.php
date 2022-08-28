<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Đăng nhập</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('css/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('css/assets/css/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{asset('img/logo1.png')}}" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="row w-100 m-0">
          <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg" style="filter: blur(8px); -webkit-filter: blur(8px);">
            <div class="card col-lg-4 mx-auto">
              <div class="card-body px-5 py-5">
                <h3 class="card-title text-center mb-3">Đăng nhập</h3>
                <form action="{{ route('post_login') }}" method="post">
                  <input type="hidden" name="_token" value="{{csrf_token()}}">
                  <div class="form-group">
                    <label>Số điện thoại hoặc email *</label>
                    <input type="text" class="form-control p_input" style="color: #fff" name="email">
                  </div>
                  <div class="form-group">
                    <label>Mật khẩu *</label>
                    <input type="password" class="form-control p_input" style="color: #fff" name="password">
                  </div>
                  <div class="form-group d-flex align-items-center justify-content-between">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input"> Ghi nhớ </label>
                    </div>
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn" style="width: 100%">Đăng nhập</button>
                  </div>
                  <div class="d-flex">
                    <button type="button" class="btn btn-facebook me-2 col">
                      <i class="mdi mdi-facebook"></i> Facebook </button>
                    <button type="button" class="btn btn-google col">
                      <i class="mdi mdi-google-plus"></i> Google + </button>
                  </div>
                  <p class="sign-up">Bạn chưa có tài khoản?<a href="{{ route('register') }}"> Đăng ký</a></p>
                </form>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- row ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('css/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('css/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('css/assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('css/assets/js/misc.js') }}"></script>
    <script src="{{ asset('css/assets/js/settings.js') }}"></script>
    <script src="{{ asset('css/assets/js/todolist.js') }}"></script>
    <!-- endinject -->
  </body>
</html>