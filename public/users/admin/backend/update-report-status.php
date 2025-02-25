<?php 
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';
require_once CLASSES_PATH . 'Notification.php';


// Ensure only admin can access this page
Session::requireAdmin();

// Get POST data
$reportId = isset($_POST['report_id']) ? (int)$_POST['report_id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

// Validate input
if (!$reportId || !in_array($status, ['pending', 'in_progress', 'resolved'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Update report status
$report = new Report();
$result = $report->updateStatus($reportId, $status);

if ($result['success']) {
    // Get report details
    $reportDetails = $report->getReportById($reportId);
    
    if ($reportDetails) {
        // Create notification for the report owner
        $notification = new Notification();
        $statusText = str_replace('_', ' ', ucfirst($status)); // Convert in_progress to In progress
        $notificationMessage = "Your report about {$reportDetails['issue_type']} at {$reportDetails['location']} has been marked as {$statusText}";
        $notification->createNotification($reportDetails['user_id'],$reportId, $notificationMessage);
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
?>
