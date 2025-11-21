<?php include '../includes/header.php'; 

// THÊM NHÂN VIÊN MỚI
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password']; // Nên mã hóa MD5 trong thực tế
    $name = $_POST['fullname'];
    
    $stmt = $conn->prepare("INSERT INTO Users (Username, Password, FullName, Role) VALUES (?, ?, ?, 'staff')");
    $stmt->bind_param("sss", $user, $pass, $name);
    
    if ($stmt->execute()) {
        echo "<script>alert('Thêm nhân viên thành công!');</script>";
    } else {
        echo "<script>alert('Lỗi: Tên đăng nhập đã tồn tại.');</script>";
    }
}

// Lấy danh sách user
$res = $conn->query("SELECT * FROM Users ORDER BY Role ASC");
?>
<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage Admin</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Quản lý Tài khoản</h2>

             <!-- Form thêm -->
             <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-4">Thêm Nhân Viên Mới</h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="username" required placeholder="Tên đăng nhập" class="border p-2 rounded">
                    <input type="text" name="password" required placeholder="Mật khẩu" class="border p-2 rounded">
                    <input type="text" name="fullname" required placeholder="Họ tên đầy đủ" class="border p-2 rounded">
                    <button class="bg-green-600 text-white p-2 rounded font-bold hover:bg-green-700">Tạo Tài Khoản</button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold border-b">
                            <tr><th class="p-4">Username</th><th class="p-4">Họ tên</th><th class="p-4">Vai trò</th><th class="p-4">Trạng thái</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php while($u = $res->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-medium text-gray-800"><?php echo $u['Username']; ?></td>
                                <td class="p-4"><?php echo $u['FullName']; ?></td>
                                <td class="p-4">
                                    <?php if($u['Role']=='admin'): ?><span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Admin</span>
                                    <?php elseif($u['Role']=='staff'): ?><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">Staff</span>
                                    <?php else: ?><span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-bold">Customer</span><?php endif; ?>
                                </td>
                                <td class="p-4 text-green-600 font-medium">Active</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>