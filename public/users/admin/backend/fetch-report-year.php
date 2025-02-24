<?php
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';

Session::start();
if (!Session::isLoggedIn() || Session::get('role') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get the selected filter from the GET request
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'current_year';

$report = new Report();
$issueCounts = $report->getIssueTypes($filter);

// Format response data
$data = [];
foreach ($issueCounts as $row) {
    $data[$row['issue_type']] = $row['count'];
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $data]);
?>
