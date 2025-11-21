<!-- Thêm ID sidebar để Javascript thao tác -->
<aside id="sidebar" class="w-64 bg-white border-r border-gray-200 flex flex-col shadow-lg h-full flex-shrink-0 transition-all duration-300">
    <div class="p-5 flex items-center justify-between border-b h-16">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-plane-circle-check text-2xl text-brand-600"></i>
            <div>
                <h1 class="text-lg font-bold tracking-tight">SkyManage</h1>
                <p class="text-xs text-gray-500 uppercase font-bold"><?php echo $_SESSION['role'] ?? 'Guest'; ?></p>
            </div>
        </div>
        <!-- Nút đóng menu trên mobile -->
        <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-red-500">
            <i class="fa-solid fa-times text-xl"></i>
        </button>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto px-3 space-y-1 no-scrollbar">
        <?php
        $role = $_SESSION['role'] ?? '';
        
        // --- MENU KHÁCH HÀNG ---
        if ($role == 'customer') {
            echo '<a href="../customer/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-magnifying-glass w-6"></i> Tìm & Đặt vé</a>';
            echo '<a href="../customer/my_tickets.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-ticket w-6"></i> Lịch sử vé</a>';
            echo '<a href="../customer/support.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-headset w-6"></i> Hỗ trợ</a>';
            // Thêm dòng này:
            echo '<a href="../customer/profile.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-user-gear w-6"></i> Tài khoản của tôi</a>';
        } 
        // --- MENU NHÂN VIÊN ---
        elseif ($role == 'staff') {
            echo '<a href="../staff/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-list-check w-6"></i> Duyệt vé</a>';
            echo '<a href="../staff/support_reply.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-comments w-6"></i> Phản hồi KH</a>';
        }
        // --- MENU ADMIN ---
        elseif ($role == 'admin') {
            echo '<a href="../admin/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-chart-line w-6"></i> Tổng quan</a>';
            echo '<a href="../admin/manage_flights.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-plane w-6"></i> QL Chuyến bay</a>';
            echo '<a href="../admin/manage_users.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition"><i class="fa-solid fa-users w-6"></i> QL Tài khoản</a>';
        }
        ?>
    </nav>
    <div class="p-4 border-t">
        <a href="../logout.php" class="flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition" onclick="return confirmAction('Bạn có chắc muốn đăng xuất?')"><i class="fa-solid fa-power-off"></i> Đăng xuất</a>
    </div>
</aside>