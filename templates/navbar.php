<?php
// require_once '../src/classes/Session.php';
Session::start();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg bg-success navbar-dark sticky-top" >
    <div class="container-fluid">
        <!-- Navbar Toggle Button (for small screens) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (Session::get('role') === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link ajax-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ajax-link <?= $current_page === 'users-page.php' ? 'active' : '' ?>" href="users-page.php">User Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ajax-link <?= $current_page === 'reports-page.php' ? 'active' : '' ?>" href="reports-page.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <!-- Lost & Found (Uncomment if needed) -->
                    </li>
                <?php elseif (Session::get('role') === 'student'): ?>
                    <li class="nav-item">
                        <a class="nav-link ajax-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <!-- Lost & Found (Uncomment if needed) -->
                    </li>
                <?php endif; ?>
            </ul>

            <li class="nav-item dropdown me-3" style="list-style: none;">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="position-relative">
                        <i class="bi bi-bell fs-5"></i> 
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="font-size: 0.75rem; padding: 4px 6px;">
                            0
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg p-2" aria-labelledby="notificationDropdown" id="notificationList">
                    <li class="text-center text-muted py-2"><small>No new notifications</small></li>
                </ul>
            </li>

            <?php if (Session::isLoggedIn()): ?>
                <div class="dropdown me-3">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars(Session::get('image_path')); ?>" width="40" height="40" class="rounded-circle">
                        <span class="d-none d-sm-inline mx-1"><?php echo htmlspecialchars(Session::get('name')); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="#">New project...</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/LormaER/public/logout.php">Sign out</a></li>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    </div>
</nav>
