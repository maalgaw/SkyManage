<?php include '../includes/header.php'; 

// XỬ LÝ GỬI YÊU CẦU
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $msg = $_POST['message'];
    $subject = $_POST['subject'] ?? 'Hỗ trợ chung';
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO SupportRequests (UserID, Subject, Message, Status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iss", $userId, $subject, $msg);
    
    if ($stmt->execute()) {
        echo "<script>alert('Gửi yêu cầu thành công!');</script>";
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
}

// Load lịch sử
$supports = getSupportRequests($conn, $_SESSION['user_id']);
?>
<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Trung tâm Hỗ trợ</h2>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Form gửi -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-4">
                        <h3 class="font-bold text-lg mb-4 text-gray-800"><i class="fa-solid fa-pen-to-square text-brand-600"></i> Gửi yêu cầu mới</h3>
                        <form method="POST">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                                <input type="text" name="subject" required class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung chi tiết</label>
                                <textarea name="message" required class="w-full border p-2.5 rounded-lg h-40 focus:ring-2 focus:ring-brand-600 outline-none resize-none"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-brand-600 text-white py-2.5 rounded-lg font-bold hover:bg-brand-700 transition shadow-md">Gửi đi</button>
                        </form>
                    </div>
                </div>

                <!-- Lịch sử -->
                <div class="lg:col-span-7">
                    <h3 class="font-bold text-lg mb-4 text-gray-800"><i class="fa-solid fa-clock-rotate-left text-brand-600"></i> Lịch sử phản hồi</h3>
                    <div class="space-y-4">
                        <?php foreach ($supports as $s): ?>
                        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 <?php echo $s['reply'] ? 'border-green-500' : 'border-yellow-400'; ?>">
                            <div class="flex justify-between items-start mb-2">
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded font-bold">#REQ-<?php echo $s['id']; ?></span>
                                <span class="text-xs text-gray-400"><?php echo $s['status']=='resolved' ? 'Đã xong' : 'Đang chờ'; ?></span>
                            </div>
                            <p class="font-semibold text-gray-800 mb-3">"<?php echo htmlspecialchars($s['msg']); ?>"</p>
                            <?php if($s['reply']): ?>
                                <div class="bg-green-50 p-3 rounded-lg text-sm text-green-800 border border-green-100">
                                    <div class="font-bold mb-1"><i class="fa-solid fa-headset"></i> Nhân viên hỗ trợ</div>
                                    <?php echo htmlspecialchars($s['reply']); ?>
                                </div>
                            <?php else: ?>
                                <div class="bg-yellow-50 p-3 rounded-lg text-sm text-yellow-800 flex items-center gap-2"><i class="fa-solid fa-spinner fa-spin"></i> Đang chờ nhân viên xử lý...</div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>