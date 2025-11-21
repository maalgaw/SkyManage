<?php include '../includes/header.php'; 

// XỬ LÝ TRẢ LỜI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $reqId = $_POST['request_id'];
    $reply = $_POST['reply'];
    
    $stmt = $conn->prepare("UPDATE SupportRequests SET Reply = ?, Status = 'resolved', ResolvedAt = NOW() WHERE RequestID = ?");
    $stmt->bind_param("si", $reply, $reqId);
    
    if ($stmt->execute()) {
        echo "<script>alert('Đã gửi phản hồi!');</script>";
    }
}

// Load yêu cầu
$supports = getSupportRequests($conn, null); // Null = lấy hết
?>
<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage Staff</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Phản hồi Khách hàng</h2>
            <div class="max-w-3xl mx-auto space-y-6">
                <?php foreach ($supports as $s): if($s['status'] == 'pending'): ?>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-yellow-400"></div>
                    <div class="flex justify-between items-center mb-4 pl-2">
                        <h4 class="font-bold text-gray-800"><?php echo $s['user']; ?></h4>
                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded font-bold">Mới</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 mb-4 border border-gray-100 italic">"<?php echo htmlspecialchars($s['msg']); ?>"</div>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $s['id']; ?>">
                        <div class="flex gap-2">
                            <input type="text" name="reply" required placeholder="Nhập nội dung trả lời..." class="flex-1 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-brand-600 outline-none text-sm">
                            <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-700 text-sm">Gửi</button>
                        </div>
                    </form>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>