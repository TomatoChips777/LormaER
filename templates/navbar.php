<?php
// require_once '../src/classes/Session.php';
Session::start();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg bg-success navbar-dark sticky-top">
    <div class="container-fluid">
        <!-- <a class="navbar-brand" href="/new_project/index.php">HOME</a> -->
        <a class="btn btn-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
            <span class="navbar-toggler-icon"></span>
        </a>

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
                        <!-- <a class="nav-link ajax-link <?= $current_page === 'lost_found.php' ? 'active' : '' ?>" href="../student/lost_found.php">Lost & Found</a> -->
                    </li>
                <?php elseif (Session::get('role') === 'student'): ?>
                    <li class="nav-item">
                        <a class="nav-link ajax-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <!-- <a class="nav-link ajax-link <?= $current_page === 'lost_found.php' ? 'active' : '' ?>" href="../student/lost_found.php">Lost & Found</a> -->
                    </li>
                <?php endif; ?>
            </ul>

            <li class="nav-item dropdown me-3">
                <a class="nav-link dropdown-toggle text-white position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount">
                        0
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationDropdown" id="notificationList" style="width: 300px; max-height: 400px; overflow-y: auto;">
                    <li class="text-center"><small>No new notifications</small></li>
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