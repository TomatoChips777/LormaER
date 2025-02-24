<?php
require_once __DIR__ . '/../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';

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

    <div class="container-fluid py-3 mx-auto">
        <div class="row mb-4">
            <!-- Admin Reports -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body">
                        <h6 class="mb-2 text-muted">Admin</h6>
                        <input type="hidden" id="user_id" value="<?php echo Session::get('id'); ?>">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-danger bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-shield-lock text-danger fs-4"></i> <!-- Shield icon for Admin -->
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Administrator</h6>
                                <h3 class="mb-0" id="adminCount"></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Reports -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body">
                        <h6 class="mb-2 text-muted">Student</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-person text-info fs-4"></i> <!-- Person icon for Student -->
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Student</h6>
                                <h3 class="mb-0" id="studentCount"></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Other Reports -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body">
                        <h6 class="mb-2 text-muted">Other</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-people text-success fs-4"></i> <!-- People icon for Other users -->
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Other</h6>
                                <h3 class="mb-0" id="otherCount"></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">All Users</h5>
                    </div>
                    <div class="col-auto">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search users...">
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" id="userRoleFilter">
                            <button type="button" class="btn btn-outline-dark active" value="all" data-filter="all">All</button>
                            <button type="button" class="btn btn-outline-dark" value="admin" data-filter="admin">Admin</button>
                            <button type="button" class="btn btn-outline-dark" value="student" data-filter="student">Student</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created At</th>
                                <th>Role</th>
                                <th>Report Count</th>
                                <th width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody"></tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span id="totalRecords"></span>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0" id="pagination"></ul>
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
    <script src="ajax/fetch-users.js">

    </script>
</body>

</html>