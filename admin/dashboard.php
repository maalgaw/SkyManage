<?php include '../includes/header.php'; 
$stats = getStats($conn); // Lấy thống kê thật
?>
<div class="flex w-full h-full">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex items-center gap-4 md:hidden shrink-0 z-10">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand-600"><i class="fa-solid fa-bars text-2xl"></i></button>
            <span class="font-bold text-lg text-brand-600">SkyManage Admin</span>
        </header>

        <div class="flex-1 overflow-auto p-4 md:p-8 fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tổng quan Hệ thống</h2>
            
            <!-- Overview Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-xl shadow-lg p-6 text-white">
                    <p class="text-brand-100 text-xs font-bold uppercase">Tổng Doanh Thu</p>
                    <h3 class="text-2xl font-bold mt-1"><?php echo number_format($stats['revenue']/1000000, 1); ?> Tỷ</h3>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <p class="text-gray-500 text-xs font-bold uppercase">Tổng Chuyến bay</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['flights']; ?></h3>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <p class="text-gray-500 text-xs font-bold uppercase">Khách hàng</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['users']; ?></h3>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <p class="text-gray-500 text-xs font-bold uppercase">Nhân viên</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['staff']; ?></h3>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center text-gray-400 h-64 flex flex-col justify-center">
                <i class="fa-solid fa-chart-column text-4xl mb-2"></i>
                <p>[Khu vực hiển thị Biểu đồ Thống kê]</p>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>