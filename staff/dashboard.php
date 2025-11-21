<?php include '../includes/header.php'; 

// XỬ LÝ DUYỆT/HỦY VÉ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $bookingId = $_POST['booking_id'];
    $status = $_POST['action'] == 'approve' ? 'approved' : 'cancelled';
    
    $stmt = $conn->prepare("UPDATE Bookings SET Status = ? WHERE BookingID = ?");
    $stmt->bind_param("si", $status, $bookingId);
    
    if ($stmt->execute()) {
        // Nếu hủy vé thì trả lại ghế
        if ($status == 'cancelled') {
             $flightId = $_POST['flight_id']; // Cần gửi kèm FlightID trong form
             $conn->query("UPDATE Flights SET AvailableSeats = AvailableSeats + 1 WHERE FlightID = '$flightId'");
        }
        echo "<script>alert('Cập nhật trạng thái thành công!');</script>";
    }
}

// Lấy dữ liệu
$stats = getStats($conn);
$allTickets = getUserTickets($conn, null); // Null = Lấy hết
?>
<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage Staff</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Nhân viên</h2>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between">
                    <div><p class="text-gray-500 text-xs font-bold uppercase">Vé chờ duyệt</p><p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $stats['tickets_pending']; ?></p></div>
                    <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 text-xl"><i class="fa-solid fa-ticket"></i></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                    <div><p class="text-gray-500 text-xs font-bold uppercase">Doanh thu</p><p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($stats['revenue']/1000000, 1); ?>M</p></div>
                    <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-600 text-xl"><i class="fa-solid fa-sack-dollar"></i></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
                    <div><p class="text-gray-500 text-xs font-bold uppercase">Tổng khách hàng</p><p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $stats['users']; ?></p></div>
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-xl"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>

            <!-- Ticket Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-gray-800">Danh sách đặt vé cần xử lý</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                            <tr><th class="p-4">Mã Vé</th><th class="p-4">Khách hàng</th><th class="p-4">Chuyến bay</th><th class="p-4">Ngày đặt</th><th class="p-4 text-center">Thao tác</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php foreach ($allTickets as $t): if($t['status'] == 'pending'): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-mono font-bold"><?php echo $t['id']; ?></td>
                                <td class="p-4"><?php echo $t['customer']; ?></td>
                                <td class="p-4 text-brand-600 font-medium"><?php echo $t['flight']; ?></td>
                                <td class="p-4 text-gray-500"><?php echo $t['date']; ?></td>
                                <td class="p-4 text-center flex justify-center gap-2">
                                    <form method="POST" onsubmit="return confirm('Duyệt vé này?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $t['raw_id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button class="bg-green-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-green-700">Duyệt</button>
                                    </form>
                                    <form method="POST" onsubmit="return confirm('Hủy vé này?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $t['raw_id']; ?>">
                                        <input type="hidden" name="flight_id" value="<?php echo $t['flight']; ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button class="bg-red-100 text-red-600 px-3 py-1.5 rounded text-xs font-bold hover:bg-red-200">Hủy</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>