<?php include '../includes/header.php'; 

$userId = $_SESSION['user_id'];
$msg = '';
$msgType = ''; // 'success' hoặc 'error'

// Lấy thông tin user hiện tại
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// XỬ LÝ: CẬP NHẬT THÔNG TIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob']; // YYYY-MM-DD

    // Nếu ngày sinh rỗng, chuyển thành NULL để tránh lỗi date
    if (empty($dob)) $dob = NULL;

    $updStmt = $conn->prepare("UPDATE Users SET FullName=?, Email=?, Phone=?, DateOfBirth=? WHERE UserID=?");
    $updStmt->bind_param("ssssi", $fullname, $email, $phone, $dob, $userId);

    if ($updStmt->execute()) {
        $msg = "Cập nhật thông tin thành công!";
        $msgType = "success";
        // Refresh lại dữ liệu user
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $_SESSION['user'] = $fullname; // Cập nhật tên hiển thị trên header
    } else {
        $msg = "Lỗi: " . $conn->error;
        $msgType = "error";
    }
}

// XỬ LÝ: ĐỔI MẬT KHẨU
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_pass'])) {
    $oldPass = $_POST['old_pass'];
    $newPass = $_POST['new_pass'];
    $confirmPass = $_POST['confirm_pass'];

    // Kiểm tra mật khẩu cũ
    if ($oldPass !== $user['Password']) {
        $msg = "Mật khẩu cũ không chính xác!";
        $msgType = "error";
    } elseif ($newPass !== $confirmPass) {
        $msg = "Mật khẩu xác nhận không khớp!";
        $msgType = "error";
    } else {
        $passStmt = $conn->prepare("UPDATE Users SET Password=? WHERE UserID=?");
        $passStmt->bind_param("si", $newPass, $userId);
        if ($passStmt->execute()) {
            $msg = "Đổi mật khẩu thành công!";
            $msgType = "success";
            // Refresh lại dữ liệu user
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            $msg = "Lỗi đổi mật khẩu: " . $conn->error;
            $msgType = "error";
        }
    }
}
?>

<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <!-- Mobile Header -->
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Thông tin tài khoản</h2>

            <!-- Thông báo -->
            <?php if($msg): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $msgType == 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                    <i class="<?php echo $msgType == 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-triangle-exclamation'; ?> mr-2"></i>
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Cột 1: Thông tin chung -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2"><i class="fa-regular fa-id-card mr-2 text-brand-600"></i>Thông tin cá nhân</h3>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                            <!-- SỬA LỖI Ở ĐÂY: Thêm ?? '' để xử lý NULL -->
                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['FullName'] ?? ''); ?>" required class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>" disabled class="w-full border p-2.5 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <!-- SỬA LỖI Ở ĐÂY -->
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <!-- SỬA LỖI Ở ĐÂY (Dòng gây lỗi trước đó) -->
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                            <input type="date" name="dob" value="<?php echo $user['DateOfBirth']; ?>" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                        </div>
                        
                        <div class="col-span-2 mt-4 flex justify-end">
                            <button type="submit" name="update_info" class="bg-brand-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-brand-700 transition shadow-md">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Cột 2: Đổi mật khẩu -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                    <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2"><i class="fa-solid fa-lock mr-2 text-brand-600"></i>Đổi mật khẩu</h3>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                            <input type="password" name="old_pass" required class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                            <input type="password" name="new_pass" required class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nhập lại mật khẩu mới</label>
                            <input type="password" name="confirm_pass" required class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                        </div>
                        <button type="submit" name="change_pass" class="w-full bg-gray-800 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-black transition shadow-md">
                            Cập nhật mật khẩu
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>