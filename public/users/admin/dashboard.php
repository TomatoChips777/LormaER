<?php
require_once __DIR__ . '/../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';

if(!Session::isLoggedIn()){
    header('Location: ../index.php');
}
// Ensure only admin can access this page
Session::requireAdmin();

?>
<!DOCTYPE html>
<html lang="en">
<?php require_once TEMPLATES_PATH . 'head.php'; ?>

<body class="bg-light">
    <?php 
    require_once TEMPLATES_PATH . 'navbar.php';
    require_once TEMPLATES_PATH . 'sidebar.php';
    ?>


    <div class="container-fluid py-3">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-success text-white">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <i class="bi bi-tools display-4"></i>
                            </div>
                            <div class="col">
                                <h2 class="mb-0">Maintenance Reports Dashboard</h2>
                                <p class="mb-0">Manage and respond to campus maintenance reports</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4" >
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-0 shadow-sm ">
                    <div class="card-body">
                        <h3 class="mb-1 text-center">Reports Chart</h3>
                        <!-- Date filter dropdown -->
                        <label for="dateFilter">Filter by:</label>
                        <select id="dateFilter" class="form-select">
                            <option value="current_week">This Week</option>
                            <option value="current_year">Current Year</option>
                            <option value="current_month">Current Month</option>
                            <option value="last_week">Last Week</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_year">Last Year</option>
                        </select>
                        <canvas id="issueChart" height="270"></canvas>
                        <!-- <a href="reports_page.php" class="btn btn-primary  text-decoration-none">Check Reports</a> -->
                    </div>

                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div data-filter-card="in_progress" class="card h-100 border-0 shadow-sm ">
                    <div class="card-body">
                        <h3 class="mb-1 text-center">Report Status Summary</h3>
                        <label for="reportTypeFilter">Filter by:</label>
                        <select id="reportTypeFilter" class="form-select">
                            <option value="all">ALL</option>
                            <option value="plumbing">Plumbing Issue</option>
                            <option value="electrical">Electrical Problem</option>
                            <option value="structural">Structural Damage</option>
                            <option value="cleaning">Cleaning Required</option>
                            <option value="safety">Safety Concern</option>
                            <option value="other">Other</option>
                        </select>
                        <canvas id="reportChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <a data-filter-card="resolved" class="card h-100 border-0 shadow-sm ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Resolved</h6>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>

    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->

    <!--  Custom script -->
    <!-- <script src="js/status_report.js"></script> -->

    <!--  Custom script -->
    <script src="ajax/dashboard.js"></script>

    <!--  Custom script -->


</body>

</html>