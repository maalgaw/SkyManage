<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Chặn truy cập nếu chưa đăng nhập (trừ trang index)
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['user']) && $current_page != 'index.php') {
    header("Location: ../index.php");
    exit();
}
include 'db_connect.php'; // Load dữ liệu mẫu
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyManage System</title>
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 600: '#2563eb', 700: '#1d4ed8', 50: '#eff6ff' } } } }
        }
    </script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 h-screen flex overflow-hidden">
    
    <!-- Overlay cho Mobile (Ẩn mặc định) -->
    <div id="sidebar-overlay"></div>