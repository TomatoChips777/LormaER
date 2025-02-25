<?php
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';

// Initialize session
Session::start();

// Check if user is logged in and is admin
if (!Session::isLoggedIn() || Session::get('role') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Check if report ID is provided
if (!isset($_GET['report_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Report ID is required']);
    exit;
}

try {
    $report = new Report();
    $reportDetails = $report->getReportById($_GET['report_id']);

    if ($reportDetails) {
        // Format the date
        $reportDetails['date'] = date('F d, Y h:i A', strtotime($reportDetails['created_at']));
        
        // Send the response
        header('Content-Type: application/json');
        echo json_encode($reportDetails);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Report not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch report details']);
}
