<?php
require_once dirname(__DIR__) . '../src/classes/Session.php';
Session::start();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Student Management System">
    <title>LORMAFIX</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/LormaER/public/assets/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" href="/LormaER/public/assets/icon/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add this to your head tag in HTML -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <!-- <script src="/new_project/node_modules/chart.js/dist/chart.umd.js"></script> -->
    <!-- Custom CSS -->
    <style>
    /* Active page button styles */
    .page-item.active .page-link {
        background-color: rgb(33, 37, 41); /* Dark background for active button */
        color: #fff; /* White text color for active button */
        border-color: #343a40; /* Dark border for active button */
    }

    /* Default link style */
    .page-item .page-link {
        color: black; /* Default text color for page links */
    }

    /* Hover and Focus states */
    .page-item .page-link:hover, 
    .page-item .page-link:focus {
        color: #fff; /* Text color should be white on hover/focus */
        background-color: black; /* Black background on hover/focus */
        border-color: #343a40; /* Dark border for consistency */
    }

    /* Ensure active page maintains dark styling on hover/focus */
    .page-item.active .page-link:hover, 
    .page-item.active .page-link:focus {
        background-color: rgb(33, 37, 41); /* Keep active page background dark */
        color: #fff; /* Keep the text white */
        border-color: #343a40; /* Keep the border dark */
    }
</style>



</head>