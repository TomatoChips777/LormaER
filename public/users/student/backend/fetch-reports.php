<?php
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';


// Ensure only students can access this page
Session::start();
if (!Session::isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}
// Ensure only admin can access this page
$user_id = Session::get('id');

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$offset = ($page - 1) * $limit;

// Debug request parameters
error_log("Request parameters - Page: $page, Limit: $limit, Offset: $offset, Status: $status, Search: $search, Useser ID: $user_id");

// Create Report instance
$report = new Report();

// Get filtered and paginated reports
$reports = $report->getPaginatedReportsByUserId($user_id, $status, $search, $limit, $offset);
$total = $report->getTotalReportsByUserId($user_id, $status, $search);

$statusCount = $report->getReportStatusCount($user_id);
// Debug output
error_log("Reports returned: " . print_r($reports, true));
error_log("Total records: $total");

$maxDescriptionLength = 50;
$data = [];

if (!empty($reports)) {
    foreach ($reports as $report) {
        // Truncate description if it's longer than max length
        $truncatedDescription = strlen($report['description']) > $maxDescriptionLength ?
            substr($report['description'], 0, $maxDescriptionLength) . '...' :
            $report['description'];

        $data[] = [
            'id' => $report['id'],
            'date' => date('M d, Y', strtotime($report['created_at'])),
            'reporter_name' => htmlspecialchars($report['reporter_name']),
            'location' => htmlspecialchars($report['location']),
            'issue_type' => ucfirst(htmlspecialchars($report['issue_type'])),
            'description' => htmlspecialchars($truncatedDescription),
            'full_description' => htmlspecialchars($report['description']),
            'status' => $report['status']
        ];
    }
}

$statusStats = [];
foreach ($statusCount as $status) {
    $statusStats[$status['status']] = $status['count'];
}
// Debug final output
error_log("Final JSON data: " . json_encode([
    'data' => $data,
    'total' => $total,
    'pages' => ceil($total / $limit)
]));

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'user_id' =>$user_id,
    'data' => $data,
    'total' => $total,
    'pages' => ceil($total / $limit),
    'stats' => $statusStats,
]);
