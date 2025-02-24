<div class="offcanvas offcanvas-start bg-dark text-white" style="max-width: 250px;" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column p-3">

    <!-- User Profile Section -->
    <?php if (Session::isLoggedIn()): ?>
      <div class="text-center mb-3">
        <img src="<?php echo htmlspecialchars(Session::get('image_path')); ?>" width="60" height="60" class="rounded-circle border border-white">
        <h6 class="mt-2"><?php echo htmlspecialchars(Session::get('name')); ?></h6>
        <span class="badge bg-success"><?php echo ucfirst(Session::get('role')); ?></span>
      </div>
      <hr class="text-white">
    <?php endif; ?>

    <!-- Navigation Links -->
    <ul class="nav flex-column">
      <?php if (Session::get('role') === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link text-white <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?= $current_page === 'users-page.php' ? 'active' : '' ?>" href="users-page.php">
            <i class="bi bi-people"></i> User Management
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?= $current_page === 'reports-page.php' ? 'active' : '' ?>" href="reports-page.php">
            <i class="bi bi-clipboard-data"></i> Reports
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?= $current_page === 'lost_found.php' ? 'active' : '' ?>" href="lost_found.php">
            <i class="bi bi-search"></i> Lost & Found
          </a>
        </li>
      <?php elseif (Session::get('role') === 'student'): ?>
        <li class="nav-item">
          <a class="nav-link text-white <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-house-door"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?= $current_page === 'lost_found.php' ? 'active' : '' ?>" href="lost_found.php">
            <i class="bi bi-search"></i> Lost & Found
          </a>
        </li>
      <?php endif; ?>
    </ul>

    <hr class="text-white">

    <!-- Logout Button -->
    <?php if (Session::isLoggedIn()): ?>
      <div class="text-center">
        <a href="/LormaER/public/logout.php" class="btn btn-danger w-100">
          <i class="bi bi-box-arrow-right"></i> Sign Out
        </a>
      </div>
    <?php endif; ?>

  </div>
</div>
