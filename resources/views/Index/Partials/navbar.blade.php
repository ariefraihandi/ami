<nav
class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar">
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
  <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
    <i class="bx bx-menu bx-sm"></i>
  </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper mb-0">
      <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
        <i class="bx bx-search bx-sm"></i>
        <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
      </a>
    </div>
  </div>
  <!-- /Search -->

  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <!-- Style Switcher -->
    <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="bx bx-sm"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
            <span class="align-middle"><i class="bx bx-sun me-2"></i>Light</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
            <span class="align-middle"><i class="bx bx-moon me-2"></i>Dark</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
            <span class="align-middle"><i class="bx bx-desktop me-2"></i>System</span>
          </a>
        </li>
      </ul>
    </li>
    <!-- / Style Switcher-->

    <!-- Notification -->
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
      <a
        class="nav-link dropdown-toggle hide-arrow"
        href="javascript:void(0);"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false">
        <i class="bx bx-bell bx-sm"></i>
        <span class="badge bg-danger rounded-pill badge-notifications">5</span>
      </a>
      
    </li>
    <!--/ Notification -->
    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ asset('assets') }}/img/staff/dedy.png" alt="" class="w-40 h-40 rounded-circle"/>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="pages-account-settings-account.html">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{ asset('assets') }}/img/staff/dedy.png" alt="" class="w-40 h-40 rounded-circle"/>
                </div>
              </div>
              <div class="flex-grow-1">
                <span class="fw-medium d-block">John Doe</span>
                <small class="text-muted">Admin</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="pages-profile-user.html">
            <i class="bx bx-user me-2"></i>
            <span class="align-middle">My Profile</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="pages-account-settings-account.html">
            <i class="bx bx-cog me-2"></i>
            <span class="align-middle">Settings</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="pages-account-settings-billing.html">
            <span class="d-flex align-items-center align-middle">
              <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
              <span class="flex-grow-1 align-middle">Billing</span>
              <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
            </span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="pages-faq.html">
            <i class="bx bx-help-circle me-2"></i>
            <span class="align-middle">FAQ</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="pages-pricing.html">
            <i class="bx bx-dollar me-2"></i>
            <span class="align-middle">Pricing</span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="auth-login-cover.html" target="_blank">
            <i class="bx bx-power-off me-2"></i>
            <span class="align-middle">Log Out</span>
          </a>
        </li>
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>

<!-- Search Small Screens -->
<div class="navbar-search-wrapper search-input-wrapper d-none">
  <input
    type="text"
    class="form-control search-input container-xxl border-0"
    placeholder="Search..."
    aria-label="Search..." />
  <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
</div>
</nav>