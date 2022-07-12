<style>
  .sidebar .nav.sub-menu .nav-item .nav-link:hover {
    transform: scale(1.05);
    transition: 0.2s;
  }
</style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
    <a class="sidebar-brand brand-logo" href="{{ route('admin.dashboard') }}"><img src="{{ asset('css/assets/images/logo.png') }}" alt="logo" style="margin: 0;"/></a>
    <a class="sidebar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}"><img src="{{ asset('css/assets/images/logo-mini.png') }}" alt="logo" /></a>
  </div>
  <ul class="nav">
    <li class="nav-item profile">
      <div class="profile-desc">
        <div class="profile-pic">
          <div class="count-indicator">
            <img class="img-xs rounded-circle " src="{{ asset('css/assets/images/faces/face.png') }}" alt="">
            <span class="count bg-success"></span>
          </div>
          <div class="profile-name">
            <h5 class="mb-0 font-weight-normal">{{ auth()->guard('user')->user()->full_name }}</h5>
            <span>Admin</span>
          </div>
        </div>
        <a href="#" id="profile-dropdown" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
        <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
          <a href="#" class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="mdi mdi-settings text-primary"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject ellipsis mb-1 text-small">Tài khoản</p>
            </div>
          </a>
          <div class="dropdown-divider"></div>
        </div>
      </div>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <span class="menu-icon">
          <i class="mdi mdi-speedometer"></i>
        </span>
        <span class="menu-title">Trang chủ</span>
      </a>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <span class="menu-icon">
          <i class="mdi mdi-laptop"></i>
        </span>
        <span class="menu-title">Phát triển phim</span>
        <i class="menu-arrow" style="color: #fff;"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('admin.movie.create') }}"><span class="menu-icon"><i style="color: #4ad1d5" class="mdi mdi-plus"></i></span> Tạo mới </a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('admin.movie.develop') }}"><span class="menu-icon"><i style="color: #4ad1d5" class="mdi mdi-trending-up"></i></span> Thiết lập phim </a></li>
          <!-- {{ route('user.home', \Illuminate\Support\Facades\Auth::guard('user')->id()) }}. {{ route('admin.movie.website') }} -->
          <li class="nav-item"> <a class="nav-link" href="{{ route('user.home', \Illuminate\Support\Facades\Auth::guard('user')->id()) }}"><span class="menu-icon"><i style="color: #4ad1d5" class="mdi mdi-film"></i></span> Website của bạn </a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-news" aria-expanded="false" aria-controls="ui-news">
        <span class="menu-icon">
          <i class="mdi mdi-newspaper"></i>
        </span>
        <span class="menu-title">Tin tức</span>
        <i class="menu-arrow" style="color: #fff;"></i>
      </a>
      <div class="collapse" id="ui-news">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('admin.news.list') }}"><span class="menu-icon"><i style="color: #4ad1d5" class="mdi mdi-format-list-bulleted"></i></span> Danh sách </a></li>
        </ul>
      </div>
    </li>
    @if(\Illuminate\Support\Facades\Auth::guard('user')->user()->role)
    <li class="nav-item menu-items">
      <a class="nav-link" href="{{ route('admin.user.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-star"></i>
        </span>
        <span class="menu-title">Người phát triển</span>
      </a>
    </li>
    @endif
  </ul>
</nav>