<?php
session_start();

if (!isset($_SESSION["latest_appointment"])) {
    header("Location: index.php");
    exit;
}

$appointment = $_SESSION["latest_appointment"];

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function formatDateVN(string $date): string {
    $parts = explode("-", $date);
    return count($parts) === 3 ? $parts[2] . "/" . $parts[1] . "/" . $parts[0] : $date;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduMeet - Đặt lịch thành công</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <header class="header">
        <a class="brand" href="index.php"><span class="brand-cap">◒</span> EduMeet</a>
        <nav class="nav">
            <a href="index.php">Trang chủ</a>
            <a class="active" href="index.php">Đặt lịch</a>
            <a href="index.php#lecturer">Giảng viên</a>
            <a href="appointments.php">Lịch của tôi</a>
            <a href="appointments.php">Thông báo</a>
        </nav>
        <div class="profile"><div class="avatar">♙</div><div><b>Nguyễn Văn A</b><small>SV123456</small></div><span>⌄</span></div>
    </header>

    <main class="layout">
        <aside class="sidebar-card">
            <div class="illustration">👨🏻‍💻　💬　👩🏻‍💼<br><span>　　💻　　🕘</span></div>
            <h3>Lợi ích khi đặt lịch</h3>
            <div class="benefit"><span>☑</span><div><b>Chủ động thời gian</b><p>Chọn khung giờ phù hợp với lịch của bạn.</p></div></div>
            <div class="benefit"><span>⌕</span><div><b>Tư vấn hiệu quả</b><p>Trao đổi trực tiếp với giảng viên về học tập, đồ án,...</p></div></div>
            <div class="benefit"><span>♧</span><div><b>Nhắc lịch tự động</b><p>Hệ thống sẽ gửi thông báo nhắc lịch trước buổi hẹn.</p></div></div>
            <div class="quote">“<br><strong>Đặt lịch dễ dàng<br> Kết nối tri thức</strong><br>”</div>
        </aside>

        <section class="success-card">
            <div class="success-banner">
                <div class="check">✓</div>
                <div><h1>ĐẶT LỊCH HẸN THÀNH CÔNG!</h1><p>Cảm ơn bạn đã sử dụng dịch vụ của EduMeet.<br>Thông tin lịch hẹn của bạn đã được ghi nhận.</p></div>
            </div>

            <div class="success-body">
                <div class="appointment-info">
                    <h2>THÔNG TIN LỊCH HẸN</h2>
                    <?php
                    $rows = [
                        ["Mã lịch hẹn", $appointment["id"]],
                        ["Họ và tên", $appointment["name"]],
                        ["Mã số sinh viên", $appointment["student_id"]],
                        ["Email", $appointment["email"]],
                        ["Số điện thoại", $appointment["phone"]],
                        ["Giảng viên", $appointment["lecturer"]],
                        ["Nội dung tư vấn", $appointment["subject"]],
                        ["Hình thức gặp gỡ", $appointment["meeting_type"]],
                        ["Ngày hẹn", formatDateVN($appointment["date"])],
                        ["Khung giờ", $appointment["time"]],
                        ["Nội dung chi tiết", $appointment["detail"] ?: "Không có"]
                    ];
                    foreach ($rows as $row):
                    ?>
                        <div class="info-row"><span>▧ &nbsp;<b><?= e($row[0]) ?></b></span><strong><?= e($row[1]) ?></strong></div>
                    <?php endforeach; ?>
                </div>

                <aside class="status-box">
                    <h2>TRẠNG THÁI<br>LỊCH HẸN</h2>
                    <div class="clock">◷</div>
                    <div class="status-pill">Chờ xác nhận</div>
                    <p>Lịch hẹn của bạn đang chờ giảng viên xác nhận. Bạn sẽ nhận được email khi lịch hẹn được xác nhận hoặc có thay đổi.</p>
                    <p>Bạn có thể xem lại tại đây</p>
                    <a class="small-btn" href="appointments.php">▣ &nbsp; Lịch của tôi</a>
                </aside>
            </div>

            <div class="success-actions">
                <a href="index.php" class="outline-red">↩ Quay về trang chủ</a>
                <a href="index.php" class="outline-blue">⊕ Đặt lịch mới</a>
                <a href="appointments.php" class="btn btn-primary">▣ &nbsp; Xem lịch của tôi</a>
            </div>
        </section>
    </main>

    <div class="note">ⓘ <b>Lưu ý:</b> Vui lòng có mặt trước giờ hẹn 5-10 phút. Nếu bạn cần thay đổi hoặc hủy lịch, hãy thực hiện trước ít nhất 2 giờ.</div>

    <footer class="footer">
        <span>⚙ Cài đặt</span><span>© EduMeet. All rights reserved.</span><span>Chính sách bảo mật</span><span>Điều khoản dịch vụ</span><span>Hỗ trợ</span><span>Liên hệ</span>
    </footer>
</div>
</body>
</html>
