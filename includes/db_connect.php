<?php
/* CẤU HÌNH KẾT NỐI MYSQL */
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "SkyManageDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// --- DANH SÁCH SÂN BAY VIỆT NAM ---
$vietnam_airports = [
    'HAN' => 'Hà Nội (Nội Bài)',
    'SGN' => 'TP.HCM (Tân Sơn Nhất)',
    'DAD' => 'Đà Nẵng',
    'PQC' => 'Phú Quốc',
    'CXR' => 'Nha Trang (Cam Ranh)',
    'HPH' => 'Hải Phòng (Cát Bi)',
    'VCA' => 'Cần Thơ',
    'VII' => 'Vinh',
    'HUI' => 'Huế (Phú Bài)',
    'DLI' => 'Đà Lạt (Liên Khương)',
    'UIH' => 'Quy Nhơn (Phù Cát)',
    'VCL' => 'Chu Lai (Quảng Nam)',
    'THD' => 'Thanh Hóa (Thọ Xuân)',
    'VDH' => 'Đồng Hới',
    'TBB' => 'Tuy Hòa',
    'VKG' => 'Rạch Giá',
    'VCS' => 'Côn Đảo',
    'BMV' => 'Buôn Ma Thuột',
    'PXU' => 'Pleiku',
    'CAH' => 'Cà Mau',
    'DIN' => 'Điện Biên Phủ'
];

// --- HÀM HỖ TRỢ ---

// 1. Lấy danh sách chuyến bay
function getFlights($conn, $from = '', $to = '', $date = '') {
    $sql = "SELECT * FROM Flights WHERE 1=1";
    $types = "";
    $params = [];

    if (!empty($from)) { $sql .= " AND FromLocation LIKE ?"; $types .= "s"; $params[] = "%$from%"; }
    if (!empty($to)) { $sql .= " AND ToLocation LIKE ?"; $types .= "s"; $params[] = "%$to%"; }
    if (!empty($date)) { $sql .= " AND DATE(DepartureTime) = ?"; $types .= "s"; $params[] = $date; }
    
    $sql .= " ORDER BY DepartureTime ASC";
    $stmt = $conn->prepare($sql);
    if (!empty($params)) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $result = $stmt->get_result();

    $flights = [];
    while ($row = $result->fetch_assoc()) {
        $flights[] = [
            'id' => $row['FlightID'],
            'from' => $row['FromLocation'],
            'to' => $row['ToLocation'],
            'time' => date('d/m/Y H:i', strtotime($row['DepartureTime'])),
            'price' => $row['Price'],
            'seats' => $row['AvailableSeats']
        ];
    }
    return $flights;
}

// 2. Lấy vé của user (Kèm số ghế)
function getUserTickets($conn, $userId = null) {
    $sql = "SELECT b.*, f.FromLocation, f.ToLocation, u.FullName 
            FROM Bookings b 
            JOIN Flights f ON b.FlightID = f.FlightID
            JOIN Users u ON b.UserID = u.UserID";
    
    if ($userId) { $sql .= " WHERE b.UserID = " . intval($userId); }
    $sql .= " ORDER BY b.BookingDate DESC";

    $result = $conn->query($sql);
    $tickets = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $tickets[] = [
                'id' => 'TKT-' . str_pad($row['BookingID'], 3, '0', STR_PAD_LEFT),
                'raw_id' => $row['BookingID'],
                'flight' => $row['FlightID'],
                'flight_route' => $row['FromLocation'] . ' - ' . $row['ToLocation'],
                'customer' => $row['FullName'],
                'seat' => $row['SeatNumber'] ?? 'N/A', // Lấy số ghế
                'date' => date('d/m/Y', strtotime($row['BookingDate'])),
                'status' => $row['Status']
            ];
        }
    }
    return $tickets;
}

// 3. Lấy danh sách ghế ĐÃ ĐẶT của một chuyến bay (MỚI)
function getBookedSeats($conn, $flightId) {
    $stmt = $conn->prepare("SELECT SeatNumber FROM Bookings WHERE FlightID = ? AND Status IN ('pending', 'approved')");
    $stmt->bind_param("s", $flightId);
    $stmt->execute();
    $result = $stmt->get_result();
    $seats = [];
    while($row = $result->fetch_assoc()) {
        if(!empty($row['SeatNumber'])) {
            $seats[] = $row['SeatNumber'];
        }
    }
    return $seats;
}

// 4. Các hàm cũ (giữ nguyên)
function getSupportRequests($conn, $userId = null) { /* ... Giữ nguyên code cũ ... */ 
    $sql = "SELECT s.*, u.FullName FROM SupportRequests s JOIN Users u ON s.UserID = u.UserID";
    if ($userId) { $sql .= " WHERE s.UserID = " . intval($userId); }
    $sql .= " ORDER BY s.CreatedAt DESC";
    $result = $conn->query($sql);
    $supports = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $supports[] = [
                'id' => $row['RequestID'],
                'user' => $row['FullName'],
                'msg' => $row['Message'],
                'reply' => $row['Reply'],
                'status' => $row['Status']
            ];
        }
    }
    return $supports;
}

function getStats($conn) { /* ... Giữ nguyên code cũ ... */ 
    $stats = ['revenue' => 0, 'flights' => 0, 'users' => 0, 'staff' => 0, 'tickets_pending' => 0];
    $res = $conn->query("SELECT SUM(f.Price) as Total FROM Bookings b JOIN Flights f ON b.FlightID = f.FlightID WHERE b.Status = 'approved'");
    $stats['revenue'] = $res->fetch_assoc()['Total'] ?? 0;
    $res = $conn->query("SELECT COUNT(*) as Total FROM Flights");
    $stats['flights'] = $res->fetch_assoc()['Total'];
    $res = $conn->query("SELECT COUNT(*) as Total FROM Users WHERE Role='customer'");
    $stats['users'] = $res->fetch_assoc()['Total'];
    $res = $conn->query("SELECT COUNT(*) as Total FROM Users WHERE Role='staff'");
    $stats['staff'] = $res->fetch_assoc()['Total'];
    $res = $conn->query("SELECT COUNT(*) as Total FROM Bookings WHERE Status='pending'");
    $stats['tickets_pending'] = $res->fetch_assoc()['Total'];
    return $stats;
}

$currentUserID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>