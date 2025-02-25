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

<body>
    <div class="container-fluid">
    <div class="row">
        <!-- Sidebar (hidden on small screens) -->
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-success d-none d-lg-block">
            <?php require_once TEMPLATES_PATH . 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col">
            <?php require_once TEMPLATES_PATH . 'navbar.php'; ?>

            <div class="container-fluid text-center">
                <div class="row align-items-start">
                    <!-- Content Goes Here -->
                </div>
            </div>

            <hr>

            <div class="d-flex p-5 justify-content-center">
                <div class="card me-5">
                    <div id="consultation_chart" style="max-width: 600px; margin: 35px;">
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
                    </div>
                    <div class="card-body">
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>

                <div class="card">
                    <div id="diagnostic_chart" style="max-width: 600px; margin: 35px;">
                    <label for="reportTypeFilter">Filter by:</label>
                        <select id="reportTypeFilter" class="form-select">
                            <option value="all">ALL</option>
                            <option value="plumbing">Plumbing Issue</option>
                            <option value="electrical">Electrical Problem</option>
                            <option value="structural">Structural Damage</option>
                            <option value="cleaning">Cleaning Required</option>
                            <option value="safety">Safety Concern</option>
                            <option style="max-width: 600px; margin: 35px;">Other</option>
                        </select>
                        <canvas id="reportChart"></canvas>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>
            </div>
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
    <script src="ajax/fetch-reports.js"></script>

    <!--  Custom script -->


</body>

</html>