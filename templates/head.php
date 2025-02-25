<?php
require_once dirname(__DIR__) . '../src/classes/Session.php';
Session::start();
// Session::start();

$current_page = basename($_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" href="/LormaER/public/assets/css/custom-css.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    </style>

</head>