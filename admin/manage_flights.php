<?php include '../includes/header.php'; 

// XỬ LÝ THÊM / XÓA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // XÓA
        $id = $_POST['delete_id'];
        $conn->query("DELETE FROM Flights WHERE FlightID = '$id'");
        echo "<script>alert('Đã xóa chuyến bay!');</script>";
    } else {
        // THÊM MỚI
        $id = $_POST['id'];
        $fromCode = $_POST['from'];
        $toCode = $_POST['to'];
        
        // Lấy tên đầy đủ từ mảng
        $fromName = $vietnam_airports[$fromCode] ?? $fromCode;
        $toName = $vietnam_airports[$toCode] ?? $toCode;

        $date = $_POST['date'];
        $price = $_POST['price'];
        $seats = 60; // CỐ ĐỊNH 60 GHẾ (Theo yêu cầu)

        if ($fromCode == $toCode) {
            echo "<script>alert('Lỗi: Điểm đi và đến không được trùng nhau!');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO Flights (FlightID, FromLocation, ToLocation, DepartureTime, Price, TotalSeats, AvailableSeats) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssdii", $id, $fromName, $toName, $date, $price, $seats, $seats);
            if ($stmt->execute()) {
                 echo "<script>alert('Thêm chuyến bay thành công!');</script>";
            } else {
                 echo "<script>alert('Lỗi: Mã chuyến bay có thể bị trùng.');</script>";
            }
        }
    }
}

$flights = getFlights($conn);
?>
<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage Admin</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Quản lý Chuyến bay</h2>

            <!-- Form thêm nhanh -->
            <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-4">Thêm chuyến bay mới</h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã chuyến bay</label>
                        <input type="text" name="id" required placeholder="VD: VN123" class="w-full border p-2 rounded bg-gray-50 focus:ring-2 focus:ring-brand-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày giờ bay</label>
                        <input type="datetime-local" name="date" required class="w-full border p-2 rounded bg-gray-50 focus:ring-2 focus:ring-brand-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm đi</label>
                        <select name="from" required class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-brand-600 outline-none">
                            <option value="">-- Chọn Sân bay đi --</option>
                            <?php foreach ($vietnam_airports as $code => $name): ?>
                                <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm đến</label>
                        <select name="to" required class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-brand-600 outline-none">
                            <option value="">-- Chọn Sân bay đến --</option>
                            <?php foreach ($vietnam_airports as $code => $name): ?>
                                <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá vé (VNĐ)</label>
                        <input type="number" name="price" required placeholder="VD: 1500000" class="w-full border p-2 rounded bg-gray-50 focus:ring-2 focus:ring-brand-600 outline-none">
                    </div>
                    <div class="flex items-end">
                        <button class="w-full bg-brand-600 text-white p-2 rounded font-bold hover:bg-brand-700 transition">
                            <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu Chuyến Bay
                        </button>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2 italic">* Số ghế mặc định là 60 ghế/chuyến.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold border-b">
                            <tr><th class="p-4">Mã CB</th><th class="p-4">Hành trình</th><th class="p-4">Thời gian</th><th class="p-4">Giá vé</th><th class="p-4">Ghế trống</th><th class="p-4 text-center">Thao tác</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php foreach ($flights as $f): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-mono font-bold text-brand-600"><?php echo $f['id']; ?></td>
                                <td class="p-4"><?php echo $f['from']; ?> <i class="fa-solid fa-arrow-right text-xs mx-1"></i> <?php echo $f['to']; ?></td>
                                <td class="p-4"><?php echo $f['time']; ?></td>
                                <td class="p-4 font-bold"><?php echo number_format($f['price']); ?> đ</td>
                                <td class="p-4"><?php echo $f['seats']; ?>/60</td>
                                <td class="p-4 text-center">
                                    <form method="POST" onsubmit="return confirm('Xóa chuyến bay này?');" class="inline">
                                        <input type="hidden" name="delete_id" value="<?php echo $f['id']; ?>">
                                        <button class="text-red-600 hover:bg-red-50 p-2 rounded"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>