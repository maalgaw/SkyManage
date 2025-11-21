// Hàm Toggle Sidebar (Dùng cho Mobile)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
    
    if (overlay) {
        overlay.classList.toggle('active');
    }
}

// Tự động đóng Sidebar khi click ra ngoài (vào overlay)
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }
});

// Hàm xác nhận chung (Dùng cho nút Xóa hoặc Hủy)
// Sử dụng: onclick="return confirmAction('Bạn có chắc muốn xóa vé này?')"
function confirmAction(message) {
    return confirm(message || "Bạn có chắc chắn muốn thực hiện hành động này?");
}

// Hàm hiển thị thông báo tạm thời (Toast) - Nâng cao (Optional)
function showToast(message, type = 'success') {
    // Bạn có thể mở rộng phần này để tạo thông báo đẹp hơn thay vì alert
    // Hiện tại dùng alert cho đơn giản
    alert(message);
}