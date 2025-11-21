<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'includes/db_connect.php';

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];

    // Validate cơ bản
    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra tên đăng nhập đã tồn tại chưa
        $checkStmt = $conn->prepare("SELECT UserID FROM Users WHERE Username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Tên đăng nhập này đã được sử dụng!";
        } else {
            // Insert dữ liệu (Role mặc định là 'customer')
            // Lưu ý: Để đơn giản cho bài tập, mật khẩu đang lưu dạng text thường.
            // Thực tế nên dùng: password_hash($password, PASSWORD_DEFAULT)
            $stmt = $conn->prepare("INSERT INTO Users (Username, Password, FullName, Email, Phone, DateOfBirth, Role) VALUES (?, ?, ?, ?, ?, ?, 'customer')");
            $stmt->bind_param("ssssss", $username, $password, $fullname, $email, $phone, $dob);

            if ($stmt->execute()) {
                $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
                // Xóa dữ liệu form để tránh submit lại
                $username = $fullname = $email = $phone = $dob = ""; 
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
        $checkStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - SkyManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 600: '#2563eb', 700: '#1d4ed8', 50: '#eff6ff', 900: '#1e3a8a' } } } }
        }
    </script>
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
<body class="bg-pattern flex items-center justify-center min-h-screen p-4">
    
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-2xl fade-in border border-gray-100 my-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Đăng ký tài khoản</h2>
            <p class="text-gray-500 text-sm mt-1">Trở thành thành viên của SkyManage</p>
        </div>

        <!-- Thông báo Lỗi -->
        <?php if($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <div><p class="font-bold text-sm">Đăng ký thất bại</p><p class="text-sm"><?php echo $error; ?></p></div>
            </div>
        <?php endif; ?>

        <!-- Thông báo Thành công -->
        <?php if($success): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-check-circle mt-0.5"></i>
                <div>
                    <p class="font-bold text-sm">Thành công</p>
                    <p class="text-sm"><?php echo $success; ?> <a href="index.php" class="underline font-bold ml-1 hover:text-green-800">Đăng nhập ngay</a></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cột trái: Thông tin cá nhân -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-700 border-b pb-2 text-sm uppercase text-brand-600">Thông tin cá nhân</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" name="fullname" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>"
                           class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none" placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none" placeholder="email@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                           class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none" placeholder="0912...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                    <input type="date" name="dob" required value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>"
                           class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                </div>
            </div>

            <!-- Cột phải: Thông tin tài khoản -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-700 border-b pb-2 text-sm uppercase text-brand-600">Tài khoản</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none bg-gray-50" placeholder="user123">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <input type="password" name="password" required 
                           class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none" placeholder="******">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nhập lại mật khẩu</label>
                    <input type="password" name="confirm_password" required 
                           class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none" placeholder="******">
                </div>
            </div>

            <div class="col-span-1 md:col-span-2 mt-4 pt-4 border-t border-gray-100">
                <button type="submit" class="w-full bg-brand-600 text-white py-3 rounded-lg font-bold hover:bg-brand-700 transition shadow-lg transform hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fa-solid fa-user-plus mr-2"></i> Đăng ký tài khoản
                </button>
            </div>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Đã có tài khoản?</p>
            <a href="index.php" class="inline-block mt-1 text-brand-600 hover:underline font-bold">Quay lại Đăng nhập</a>
        </div>
    </div>

</body>
</html>