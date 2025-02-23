<?php
require_once __DIR__ . '/../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';

// Ensure only admin can access this page
Session::requireAdmin();

// Initialize Report class for initial counts
$report = new Report();
$allReports = $report->getAllReports();

// Count reports by status
$pendingCount = count(array_filter($allReports, function ($r) {
    return $r['status'] === 'pending';
}));
$inProgressCount = count(array_filter($allReports, function ($r) {
    return $r['status'] === 'in_progress';
}));
$resolvedCount = count(array_filter($allReports, function ($r) {
    return $r['status'] === 'resolved';
}));
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once TEMPLATES_PATH . 'head.php'; ?>
<body class="bg-light">
<?php 
    require_once TEMPLATES_PATH . 'navbar.php';
    require_once TEMPLATES_PATH . 'sidebar.php';
?>

    <div class="container py-3 mx-auto">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div data-filter-card="pending" class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Pending Reports</h6>
                                <h3 class="mb-0" id="pendingCount"><?php echo $pendingCount; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div data-filter-card="in_progress" class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-gear text-primary fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">In Progress</h6>
                                <h3 class="mb-0" id="inProgressCount"><?php echo $inProgressCount; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div data-filter-card="resolved" class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Resolved</h6>
                                <h3 class="mb-0" id="resolvedCount"><?php echo $resolvedCount; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">All Reports</h5>
                    </div>
                    <div class="col-auto">
                        <div class="input-group me-2">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search reports...">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" id="reportStatusFilter">
                            <button type="button" class="btn btn-outline-dark active" value="all" data-filter="all">All</button>
                            <button type="button" class="btn btn-outline-dark" value="pending" data-filter="pending">Pending</button>
                            <button type="button" class="btn btn-outline-dark" value="in_progress" data-filter="in_progress">In Progress</button>
                            <button type="button" class="btn btn-outline-dark" value="resolved" data-filter="resolved">Resolved</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reported By</th>
                                <th>Location</th>
                                <th>Issue Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody">
                           
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center">
                            <select id="pageSizeSelect" class="form-select me-2">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                            <span id="totalRecords"></span>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0 " id="pagination">
                                <!-- Pagination will be generated via JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom script for fetching the reports displayed in the table-->
    <script src="ajax/fetch-reports.js">
        
    </script>
</body>
</html>