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

$report = new Report();

// Get the issue_type filter from the request
$issueTypeFilter = isset($_GET['issue_type_filter']) ? $_GET['issue_type_filter'] : 'all';


// Fetch report statuses
$reportStatus = $report->getReportStatusByType($issueTypeFilter);
$issueTypeCounts = [];
$totalCounts = ['pending' => 0, 'in_progress' => 0, 'resolved' => 0];
// If filtering by all, return only total counts
if ($issueTypeFilter === 'all') {

    foreach ($reportStatus as $statusData) {
        $status = $statusData['status'];
        $count = (int) $statusData['count'];

        if (isset($totalCounts[$status])) {
            $totalCounts[$status] += $count;
        }
    }

    $response = [
        'success' => true,
        'total_counts' => $totalCounts
    ];
} else {
    foreach ($reportStatus as $statusData) {
        $issueType = $statusData['issue_type'];
        $status = $statusData['status'];
        $count = $statusData['count'];
    
        // If the issue type is not in the array, initialize it
        if (!isset($issueTypeCounts[$issueType])) {
            $issueTypeCounts[$issueType] = [
                'pending' => 0,
                'in_progress' => 0,
                'resolved' => 0
            ];
        }
    
        // Update counts for each issue type
        $issueTypeCounts[$issueType][$status] = $count;
    
        // Add count to the total count
        if (isset($totalCounts[$status])) {
            $totalCounts[$status] += $count;
        }
    }
    // Prepare response
$response = [
    'success' => true,
    'issue_type_counts' => $issueTypeCounts,
    'total_counts' => $totalCounts 
];
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
