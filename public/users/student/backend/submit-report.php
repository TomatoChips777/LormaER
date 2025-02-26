<?php
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Report.php';
require_once CLASSES_PATH . 'Notification.php';

// Check if user is logged in
Session::start();
if (!Session::isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}
$user_id = Session::get('id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $issueType = filter_input(INPUT_POST, 'issue_type', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    // Validate inputs
    if (!$location || !$issueType || !$description) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/reports/';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        // Check if it's a valid image
        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
        if ($imageInfo === false) {
            echo json_encode(['success' => false, 'message' => 'Invalid image file']);
            exit();
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/reports/' . $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    }

    // Create report
    try {
        $report = new Report();
        $result = $report->createReport($location, $issueType, $description, $imagePath);
        
        // Fetch status counts
        $statusCount = $report->getReportStatusCount($user_id);
        
        if ($result['success']) {
            // Format the date for display
            $result['report']['created_at'] = date('M d, Y', strtotime($result['report']['created_at']));
            
            // Prepare the status counts for response
            $statusStats = [];
            foreach ($statusCount as $status) {
                $statusStats[$status['status']] = $status['count'];
            }
            
            // Set proper headers
            header('Content-Type: application/json');
            
            // Send success response
            echo json_encode([
                'success' => true,
                'message' => 'Report submitted successfully',
                'report' => $result['report'],
                'stats' => $statusStats
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to submit report'
            ]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while submitting the report'
        ]);
    }

    exit();
} else {
    header('Location: ../dashboard.php');
    exit();
}
