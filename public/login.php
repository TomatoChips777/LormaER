<?php
require_once __DIR__ . '/../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once ROOT_PATH . '/src/google-auth-config/google_config.php';

$url = $client->createAuthUrl();

if (Session::isLoggedIn()) {
    if (Session::get('role') === 'admin') {
        header('Location:   users/admin/dashboard.php');
    } else  {
        header('Location: users/student/dashboard.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once TEMPLATES_PATH . 'head.php'; ?>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h3 class="mb-0">Login</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if($error = Session::getFlash('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if($success = Session::getFlash('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                    <div class="d-grid d-flex">
                                <a  href="<?= $url ?>"type="submit" class=" form-control btn btn-dark  btn-lg"><i class="bi bi-google"></i> Google Login</a>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>