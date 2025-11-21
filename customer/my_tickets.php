<?php include '../includes/header.php'; 
// Lấy vé thật của user đang đăng nhập
$myTickets = getUserTickets($conn, $_SESSION['user_id']);
?>
<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage</span>
        </header>
        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Lịch sử đặt vé</h2>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b">
                            <tr><th class="p-4">Mã Vé</th><th class="p-4">Chuyến bay</th><th class="p-4">Ngày đặt</th><th class="p-4">Trạng thái</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php foreach ($myTickets as $t): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4"><span class="font-mono font-bold text-brand-600 bg-brand-50 px-2 py-1 rounded"><?php echo $t['id']; ?></span></td>
                                <td class="p-4 font-medium text-gray-700"><?php echo $t['flight_route']; ?> (<?php echo $t['flight']; ?>)</td>
                                <td class="p-4 text-gray-500"><?php echo $t['date']; ?></td>
                                <td class="p-4">
                                    <?php if($t['status'] == 'approved'): ?>
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Đã xuất vé</span>
                                    <?php elseif($t['status'] == 'cancelled'): ?>
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Đã hủy</span>
                                    <?php else: ?>
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Chờ xử lý</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(empty($myTickets)): ?><div class="p-8 text-center text-gray-500">Bạn chưa có lịch sử đặt vé nào.</div><?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>