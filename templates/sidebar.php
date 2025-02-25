<div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100 sticky d-none d-lg-block">
    <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <h1 class="fs-10 d-none d-sm-inline"></h1>
    </a>

    <ul class="navbar-nav flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
        <!-- User Profile Section -->
        <!-- Navigation Links -->
        <?php if (Session::get('role') === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link ajax-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ajax-link <?= $current_page === 'users-page.php' ? 'active' : '' ?>" href="users-page.php">
                    <i class="bi bi-people"></i> User Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ajax-link <?= $current_page === 'reports-page.php' ? 'active' : '' ?>" href="reports-page.php">
                    <i class="bi bi-clipboard-data"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ajax-link <?= $current_page === 'lost_found.php' ? 'active' : '' ?>" href="lost_found.php">
                    <i class="bi bi-search"></i> Lost & Found
                </a>
            </li>
        <?php elseif (Session::get('role') === 'student'): ?>
            <li class="nav-item">
                <a class="nav-link ajax-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ajax-link <?= $current_page === 'lost_found.php' ? 'active' : '' ?>" href="lost_found.php">
                    <i class="bi bi-search"></i> Lost & Found
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <hr class="text-white">

    <!-- Logout Button -->
    <?php if (Session::isLoggedIn()): ?>
        <!-- <div class="text-center">
            <a href="/LormaER/public/logout.php" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Sign Out
            </a>
        </div> -->
    <?php endif; ?>
</div>
