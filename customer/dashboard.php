<?php 
// --- API NHỎ ĐỂ LẤY GHẾ ĐÃ ĐẶT (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] == 'get_booked_seats') {
    include '../includes/db_connect.php'; // Kết nối DB riêng cho request này
    $fid = $_GET['fid'];
    $booked = getBookedSeats($conn, $fid);
    header('Content-Type: application/json');
    echo json_encode($booked);
    exit; // Dừng script tại đây, không load HTML
}

include '../includes/header.php'; 

// XỬ LÝ ĐẶT VÉ (CÓ SỐ GHẾ)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_flight_id'])) {
    $flightId = $_POST['book_flight_id'];
    $seatNumber = $_POST['seat_number'];
    $userId = $_SESSION['user_id'];

    // Kiểm tra lại xem ghế đã bị ai đặt nhanh tay trước không
    $check = $conn->query("SELECT * FROM Bookings WHERE FlightID = '$flightId' AND SeatNumber = '$seatNumber' AND Status IN ('pending', 'approved')");
    
    if ($check->num_rows > 0) {
        echo "<script>alert('Rất tiếc, ghế $seatNumber vừa có người đặt. Vui lòng chọn ghế khác!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO Bookings (UserID, FlightID, SeatNumber, Status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iss", $userId, $flightId, $seatNumber);
        
        if ($stmt->execute()) {
            // Trừ ghế trống
            $conn->query("UPDATE Flights SET AvailableSeats = AvailableSeats - 1 WHERE FlightID = '$flightId'");
            echo "<script>alert('Đặt vé ghế $seatNumber thành công! Đang chờ duyệt.'); window.location.href='my_tickets.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }
}

$s_from = $_GET['from'] ?? '';
$s_to = $_GET['to'] ?? '';
$s_date = $_GET['date'] ?? '';
$flights = getFlights($conn, $s_from, $s_to, $s_date);
?>

<div class="flex w-full h-full relative">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tìm & Đặt vé máy bay</h2>
            
            <!-- Form Tìm kiếm (Dropdown) -->
            <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border border-gray-100">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm đi</label>
                        <select name="from" class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-brand-600 outline-none bg-white">
                            <option value="">-- Tất cả --</option>
                            <?php foreach ($vietnam_airports as $name): ?>
                                <option value="<?php echo $name; ?>" <?php if(strpos($s_from, $name) !== false) echo 'selected'; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm đến</label>
                         <select name="to" class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-brand-600 outline-none bg-white">
                            <option value="">-- Tất cả --</option>
                            <?php foreach ($vietnam_airports as $name): ?>
                                <option value="<?php echo $name; ?>" <?php if(strpos($s_to, $name) !== false) echo 'selected'; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đi</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($s_date); ?>" class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-brand-600 outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-brand-600 text-white p-2.5 rounded-lg font-bold hover:bg-brand-700 transition shadow-md">Tìm kiếm</button>
                    </div>
                </form>
            </div>

            <h3 class="font-bold text-gray-700 mb-4 text-lg">Kết quả tìm kiếm (<?php echo count($flights); ?>)</h3>
            <div class="grid gap-4">
                <?php foreach ($flights as $f): ?>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-5 w-full md:w-auto">
                        <div class="w-14 h-14 bg-brand-50 rounded-full flex items-center justify-center text-brand-600 text-xl"><i class="fa-solid fa-plane"></i></div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800"><?php echo $f['from']; ?> <i class="fa-solid fa-arrow-right-long text-gray-400 mx-2 text-sm"></i> <?php echo $f['to']; ?></h3>
                            <p class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-mono text-gray-600"><?php echo $f['id']; ?></span>
                                <span><i class="fa-regular fa-clock mr-1"></i> <?php echo $f['time']; ?></span>
                                <span class="text-blue-600 font-medium text-xs ml-2">Còn <?php echo $f['seats']; ?> ghế</span>
                            </p>
                        </div>
                    </div>
                    <div class="text-right w-full md:w-auto flex flex-row md:flex-col justify-between items-center md:items-end">
                        <p class="text-xl font-bold text-brand-600"><?php echo number_format($f['price']); ?> đ</p>
                        
                        <!-- Nút mở Modal Chọn Ghế -->
                        <button onclick="openSeatModal('<?php echo $f['id']; ?>', '<?php echo number_format($f['price']); ?>')" class="bg-brand-600 text-white px-6 py-2 rounded-lg mt-0 md:mt-2 text-sm font-medium hover:bg-brand-700 shadow-sm transition">
                            Chọn ghế & Đặt
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($flights)): ?>
                    <div class="text-center py-10 text-gray-500 bg-white rounded-xl border border-dashed">Không tìm thấy chuyến bay nào phù hợp.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- MODAL CHỌN GHẾ -->
    <div id="seat-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative">
            <button onclick="closeSeatModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl"><i class="fa-solid fa-times"></i></button>
            
            <h3 class="text-2xl font-bold mb-2 text-gray-800">Sơ đồ ghế ngồi - <span id="modal-flight-id" class="text-brand-600"></span></h3>
            <p class="text-sm text-gray-500 mb-6">Vui lòng chọn ghế bạn muốn ngồi (Giá vé: <span id="modal-price" class="font-bold text-red-600"></span> đ)</p>

            <!-- Chú thích -->
            <div class="flex gap-4 mb-6 justify-center text-sm">
                <div class="flex items-center gap-2"><div class="w-6 h-6 bg-gray-100 border rounded border-gray-300"></div> Trống</div>
                <div class="flex items-center gap-2"><div class="w-6 h-6 bg-red-100 border border-red-300 text-red-400 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></div> Đã đặt</div>
                <div class="flex items-center gap-2"><div class="w-6 h-6 bg-green-500 text-white border border-green-600 flex items-center justify-center"><i class="fa-solid fa-check"></i></div> Đang chọn</div>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sơ đồ ghế -->
                <div class="flex-1 bg-gray-50 p-4 rounded-xl border border-gray-200 flex justify-center">
                    <div class="grid grid-cols-7 gap-2 text-center">
                        <!-- Header Cột -->
                        <div></div>
                        <div class="font-bold text-gray-400">A</div>
                        <div class="font-bold text-gray-400">B</div>
                        <div class="font-bold text-gray-400">C</div>
                        <div class="w-4"></div> <!-- Lối đi -->
                        <div class="font-bold text-gray-400">D</div>
                        <div class="font-bold text-gray-400">E</div>
                        
                        <!-- Ghế được render bằng JS -->
                        <div id="seat-grid" class="contents"></div>
                    </div>
                </div>

                <!-- Thông tin đặt -->
                <div class="w-full md:w-64 bg-white p-4 rounded-xl border border-gray-200 h-fit">
                    <h4 class="font-bold text-gray-700 mb-4">Ghế đang chọn</h4>
                    <div id="selected-seat-display" class="text-3xl font-bold text-brand-600 text-center py-4 border-b border-dashed mb-4">--</div>
                    
                    <form method="POST">
                        <input type="hidden" name="book_flight_id" id="form-flight-id">
                        <input type="hidden" name="seat_number" id="form-seat-number">
                        <button id="btn-confirm" disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3 rounded-lg transition cursor-not-allowed">
                            Xác nhận đặt vé
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentSelectedSeat = null;

    function openSeatModal(flightId, price) {
        document.getElementById('seat-modal').classList.remove('hidden');
        document.getElementById('modal-flight-id').innerText = flightId;
        document.getElementById('modal-price').innerText = price;
        document.getElementById('form-flight-id').value = flightId;
        
        // Reset
        document.getElementById('seat-grid').innerHTML = '<div class="col-span-7 text-center py-10"><i class="fa-solid fa-spinner fa-spin text-2xl text-brand-600"></i></div>';
        document.getElementById('selected-seat-display').innerText = '--';
        document.getElementById('btn-confirm').disabled = true;
        document.getElementById('btn-confirm').classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        document.getElementById('btn-confirm').classList.remove('bg-brand-600', 'text-white', 'hover:bg-brand-700');
        currentSelectedSeat = null;

        // Fetch ghế đã đặt
        fetch(`dashboard.php?action=get_booked_seats&fid=${flightId}`)
            .then(res => res.json())
            .then(bookedSeats => {
                renderSeatMap(bookedSeats);
            });
    }

    function closeSeatModal() {
        document.getElementById('seat-modal').classList.add('hidden');
    }

    function renderSeatMap(bookedSeats) {
        const grid = document.getElementById('seat-grid');
        grid.innerHTML = '';
        const rows = 10;
        const cols = ['A', 'B', 'C', 'D', 'E']; // 5 ghế mỗi hàng (bỏ F cho đẹp layout 2-3)

        for (let r = 1; r <= rows; r++) {
            // Số hàng
            const rowLabel = document.createElement('div');
            rowLabel.className = 'flex items-center justify-center font-bold text-gray-400 text-sm';
            rowLabel.innerText = r;
            grid.appendChild(rowLabel);

            cols.forEach((c, index) => {
                if (index === 3) { // Tạo lối đi giữa C và D
                     const aisle = document.createElement('div');
                     grid.appendChild(aisle);
                }

                const seatId = `${r}${c}`; // VD: 1A, 2B
                const isBooked = bookedSeats.includes(seatId);
                
                const seatBtn = document.createElement('button');
                seatBtn.innerText = seatId;
                seatBtn.className = `w-10 h-10 rounded-lg text-xs font-bold border transition flex items-center justify-center 
                    ${isBooked 
                        ? 'bg-red-50 border-red-200 text-red-300 cursor-not-allowed' 
                        : 'bg-white border-gray-300 text-gray-600 hover:border-brand-500 hover:text-brand-600'}`;
                
                if (isBooked) {
                    seatBtn.disabled = true;
                    seatBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                } else {
                    seatBtn.onclick = () => selectSeat(seatId, seatBtn);
                }
                grid.appendChild(seatBtn);
            });
        }
    }

    function selectSeat(seatId, btnElement) {
        // Bỏ chọn ghế cũ
        if (currentSelectedSeat) {
            const prevBtn = document.querySelector(`.selected-seat`);
            if (prevBtn) {
                prevBtn.classList.remove('bg-green-500', 'text-white', 'border-green-600', 'selected-seat');
                prevBtn.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
            }
        }

        // Chọn ghế mới
        currentSelectedSeat = seatId;
        btnElement.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
        btnElement.classList.add('bg-green-500', 'text-white', 'border-green-600', 'selected-seat');

        // Update UI bên phải
        document.getElementById('selected-seat-display').innerText = seatId;
        document.getElementById('form-seat-number').value = seatId;
        
        // Enable nút xác nhận
        const confirmBtn = document.getElementById('btn-confirm');
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        confirmBtn.classList.add('bg-brand-600', 'text-white', 'hover:bg-brand-700');
    }
</script>
<?php include '../includes/footer.php'; ?>