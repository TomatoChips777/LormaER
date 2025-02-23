<?php
require_once __DIR__ . '/../config.php';
require_once CLASSES_PATH . 'Database.php';
require_once CLASSES_PATH . 'User.php';
require_once CLASSES_PATH . 'Session.php';

Session::start();

// If the user is already logged in, redirect based on their role
if (Session::get('id')) {
    $role = Session::get('role');
    if ($role === 'admin') {
        header('Location: users/admin/dashboard.php');
    } else {
        header('Location: users/student/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once TEMPLATES_PATH . 'head.php'; ?>

<body>
<!-- Collapsible navbar for additional details -->
<div class="collapse" id="navbarToggleExternalContent" data-bs-theme="dark">
    <div class="bg-success p-4">
        <h5 class="text-body-emphasis h4">Lorma Emergency Report</h5>
        <span class="text-body-secondary">
            Report maintenance issues and emergencies quickly to ensure a safe and functional campus environment.
        </span>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <button class="navbar-toggler bg-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" 
                aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Main content -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <!-- Welcome Heading and Description -->
            <h1 class="display-4">Welcome to Lorma Emergency Report System</h1>
            <p class="lead mt-4">
                A user-friendly platform for quickly reporting maintenance and emergency facility issues across Lorma Colleges.
            </p>
            
            <p class="mt-4">
                Whether it's a leaking faucet, electrical issue, or a broken door, our system ensures that your report reaches the right people for swift action.
            </p>

            <div class="mt-5">
                <!-- Login button -->
                <a href="login.php" class="btn btn-dark btn-lg">Login to Report Issues</a>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
