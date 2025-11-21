<?php
session_start();
$error = '';

// Xử lý Login (Sử dụng MySQLi thay vì sqlsrv)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Import kết nối DB
    require_once 'includes/db_connect.php'; 
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra kết nối tồn tại
    if (!isset($conn)) {
        die("Lỗi: Không tìm thấy biến kết nối database (\$conn). Vui lòng kiểm tra file includes/db_connect.php");
    }

    // Sử dụng Prepared Statement
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? AND Password = ?");
    
    if ($stmt === false) {
        die("Lỗi câu lệnh SQL: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Lưu thông tin vào Session
        $_SESSION['user'] = $user['FullName'];
        $_SESSION['role'] = $user['Role'];
        $_SESSION['user_id'] = $user['UserID'];

        // Điều hướng
        if ($user['Role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['Role'] == 'staff') {
            header("Location: staff/dashboard.php");
        } else {
            header("Location: customer/dashboard.php");
        }
        exit();
    } else {
        $error = 'Sai tên đăng nhập hoặc mật khẩu!';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SkyManage</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 600: '#2563eb', 700: '#1d4ed8', 50: '#eff6ff', 900: '#1e3a8a' } } } }
        }
    </script>

    <!-- Font Awesome & Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .bg-pattern {
            background-color: #1e3a8a;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-pattern flex items-center justify-center h-screen p-4">
    
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md fade-in border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 shadow-sm">
                <i class="fa-solid fa-plane-up"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">SkyManage</h2>
            <p class="text-gray-500 text-sm mt-1">Hệ thống quản lý vé máy bay</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <div>
                    <p class="font-bold text-sm">Lỗi đăng nhập</p>
                    <p class="text-sm"><?php echo $error; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 ml-1">Tài khoản</label>
                <div class="relative">
                    <i class="fa-regular fa-user absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="username" required 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-brand-600 outline-none transition bg-gray-50 focus:bg-white" 
                           placeholder="admin / nhanvien / khach">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 ml-1">Mật khẩu</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3 top-3 text-gray-400"></i>
                    <input type="password" name="password" required 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-brand-600 outline-none transition bg-gray-50 focus:bg-white" 
                           placeholder="••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="rounded text-brand-600 focus:ring-brand-600 border-gray-300">
                    <span class="text-gray-600">Ghi nhớ tôi</span>
                </label>
                <a href="#" class="text-brand-600 hover:underline font-medium">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="w-full bg-brand-600 text-white py-3 rounded-lg font-bold hover:bg-brand-700 transition shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 active:translate-y-0">
                Đăng nhập
            </button>
        </form>
        
        <!-- Phần thêm mới: Nút chuyển sang đăng ký -->
        <div class="mt-6 text-center pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-600">Chưa có tài khoản?</p>
            <a href="register.php" class="inline-block mt-2 text-brand-600 font-bold hover:text-brand-700 hover:underline">Đăng ký khách hàng mới</a>
        </div>

        <p class="text-center text-gray-400 text-xs mt-8">
            &copy; 2023 SkyManage System. All rights reserved.
        </p>
    </div>

</body>
</html>