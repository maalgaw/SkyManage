✈️ SkyManage - Hệ thống Quản lý Đại lý Bán vé Máy bay

SkyManage là một ứng dụng web toàn diện được thiết kế để số hóa quy trình quản lý và bán vé máy bay cho các đại lý vừa và nhỏ. Hệ thống hỗ trợ quy trình khép kín từ tìm kiếm chuyến bay, chọn ghế trực quan, đặt vé cho đến quản lý doanh thu và chăm sóc khách hàng.

(Thay thế link ảnh trên bằng ảnh chụp màn hình thực tế của dự án)

👨‍💻 Thông tin Đồ án

Môn học: Phát triển Phần mềm Mã nguồn mở

Giảng viên hướng dẫn: [Tên giảng viên nếu có]

Sinh viên thực hiện:

Trần Quang Lâm (1771020408)

Hà Tuấn Điệp (1771020153)

Lớp: CNTT 17-12

🚀 Tính năng Nổi bật

Hệ thống được phân quyền chặt chẽ cho 3 đối tượng sử dụng:

1. Khách hàng (Customer)

🔍 Tìm kiếm chuyến bay: Theo điểm đi, điểm đến (Sân bay Việt Nam) và ngày khởi hành.

💺 Sơ đồ chọn ghế (Seat Map): Xem trạng thái ghế (Trống/Đã đặt) và chọn ghế trực quan trên sơ đồ.

🎫 Đặt vé trực tuyến: Quy trình đặt vé nhanh chóng, tự động cập nhật trạng thái ghế.

📂 Quản lý cá nhân: Xem lịch sử đặt vé, chỉnh sửa thông tin cá nhân, đổi mật khẩu.

💬 Hỗ trợ: Gửi yêu cầu hỗ trợ và nhận phản hồi từ nhân viên.

2. Nhân viên (Staff)

✅ Duyệt vé (Booking Approval): Xem danh sách vé chờ, thực hiện duyệt hoặc hủy vé.

🎧 Chăm sóc khách hàng: Tiếp nhận và trả lời các yêu cầu hỗ trợ từ khách hàng.

📊 Thống kê nhanh: Xem số lượng vé chờ xử lý và doanh thu cá nhân.

3. Quản trị viên (Admin)

✈️ Quản lý Chuyến bay: Thêm, sửa, xóa chuyến bay; thiết lập giá vé và lộ trình.

👥 Quản lý Tài khoản: Cấp tài khoản cho nhân viên, khóa tài khoản vi phạm.

📈 Báo cáo Thống kê: Dashboard tổng quan về doanh thu, số lượng chuyến bay và khách hàng mới.

🛠️ Công nghệ Sử dụng

Frontend: HTML5, CSS3, Tailwind CSS, JavaScript (Vanilla).

Backend: PHP (Thuần).

Database: MySQL.

Môi trường phát triển: WampServer / XAMPP.

Công cụ: Visual Studio Code, phpMyAdmin.

⚙️ Hướng dẫn Cài đặt

Để chạy dự án trên máy cục bộ (Localhost), hãy làm theo các bước sau:

Bước 1: Chuẩn bị môi trường

Cài đặt WampServer (hoặc XAMPP) có hỗ trợ PHP và MySQL.

Đảm bảo dịch vụ Apache và MySQL đang chạy (Icon Wamp màu xanh lá).

Bước 2: Cài đặt mã nguồn

Clone repository này về thư mục www (đối với Wamp) hoặc htdocs (đối với XAMPP):

git clone [https://github.com/username-cua-ban/skymanage.git](https://github.com/username-cua-ban/skymanage.git)


Đổi tên thư mục thành skymanage (nếu cần).

Bước 3: Cài đặt Cơ sở dữ liệu

Truy cập phpMyAdmin (thường là http://localhost/phpmyadmin).

Tạo một Database mới tên là SkyManageDB.

Chọn tab Import (Nhập), chọn file database.sql nằm trong thư mục gốc của dự án và nhấn Go (Thực hiện).

Bước 4: Cấu hình kết nối

Mở file includes/db_connect.php.

Kiểm tra và chỉnh sửa thông số kết nối nếu cần (mặc định WampServer user là root, password để trống):

$servername = "localhost";
$username = "root";
$password = ""; // Điền password nếu có
$dbname = "SkyManageDB";


Bước 5: Khởi chạy

Mở trình duyệt và truy cập: http://localhost/skymanage

🔐 Tài khoản Demo

Vai trò

Tên đăng nhập

Mật khẩu

Admin

admin

123

Staff

nhanvien01

123

Customer

khachhang01

123

📂 Cấu trúc Thư mục

skymanage/
├── admin/              # Trang chức năng cho Admin
├── assets/             # CSS, JS, Images
├── customer/           # Trang chức năng cho Khách hàng
├── includes/           # Các file dùng chung (Header, Footer, DB Connect)
├── staff/              # Trang chức năng cho Nhân viên
├── database.sql        # File script tạo CSDL
├── index.php           # Trang đăng nhập
├── register.php        # Trang đăng ký
└── README.md           # Tài liệu hướng dẫn


📸 Hình ảnh Minh họa

(Bạn có thể thêm các ảnh chụp màn hình vào thư mục assets/images và link vào đây)

Trang Dashboard Khách hàng & Chọn ghế

Trang Quản lý của Admin

Made with ❤️ by Quang Lâm & Tuấn Điệp.
