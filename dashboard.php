<?php
// dashboard.php
include 'common-header.php';
?>

<header id="header" class="header fixed-top d-flex align-items-center bg-light shadow-sm">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <a href="dashboard.php" class="logo d-flex align-items-center text-decoration-none text-dark">
      <i class="fas fa-home me-2"></i>
      <span class="fw-bold">Dashboard</span>
    </a>
    <i class="fas fa-bars toggle-sidebar-btn" style="cursor: pointer;"></i>
    <nav class="header-nav ms-auto">
      <ul class="nav">
        <li class="nav-item dropdown">
          <a class="nav-link d-flex align-items-center text-dark" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
            <span class="ms-2">K. Anderson</span>
            <i class="fas fa-chevron-down ms-2"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li class="dropdown-header text-center">
              <strong>Kevin Anderson</strong>
              <p class="m-0 small">Web Designer</p>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="profile.php">
                <i class="fas fa-user me-2"></i>
                My Profile
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>
                Sign Out
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</header>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar d-none d-lg-block">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link d-flex align-items-center">
            <i class="fas fa-tachometer-alt me-2"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="profile.php" class="nav-link d-flex align-items-center">
            <i class="fas fa-user me-2"></i>
            <span>Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="settings.php" class="nav-link d-flex align-items-center">
            <i class="fas fa-cogs me-2"></i>
            <span>Settings</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="help.php" class="nav-link d-flex align-items-center">
            <i class="fas fa-question-circle me-2"></i>
            <span>Help</span>
          </a>
        </li>
      </ul>
    </nav>

    <div id="overlay" class="position-fixed w-100 h-100 bg-dark opacity-50 d-none" style="z-index: 1040;"></div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Welcome to the Dashboard</h1>
      </div>

      <p>This is your main dashboard where you can access all features.</p>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center">
              <h5 class="card-title">Profile</h5>
              <p class="card-text">Manage your profile and account settings.</p>
              <a href="profile.php" class="btn btn-primary w-100">Go to Profile</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center">
              <h5 class="card-title">Settings</h5>
              <p class="card-text">Update your preferences and configurations.</p>
              <a href="settings.php" class="btn btn-primary w-100">Go to Settings</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center">
              <h5 class="card-title">Help</h5>
              <p class="card-text">Need assistance? Visit our help section.</p>
              <a href="help.php" class="btn btn-primary w-100">Get Help</a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
  const toggleBtn = document.querySelector('.toggle-sidebar-btn');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('d-none');
    overlay.classList.toggle('d-none');
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.add('d-none');
    overlay.classList.add('d-none');
  });
</script>

<?php include 'common-footer.php'; ?>
