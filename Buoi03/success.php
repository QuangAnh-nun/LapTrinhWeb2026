<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION["latest_appointment"]) || !is_array($_SESSION["latest_appointment"])) {
    header("Location: index.php");
    exit;
}

$appointment = $_SESSION["latest_appointment"];

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function formatDateVN(string $date): string {
    $d = DateTime::createFromFormat("Y-m-d", $date);
    return $d ? $d->format("d/m/Y") : $date;
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
            <a href="index.php">Đặt lịch</a>
            <a href="index.php#lecturer">Giảng viên</a>
            <a href="appointments.php">Lịch của tôi</a>
            <a href="appointments.php">Thông báo</a>
        </nav>
        <div class="profile"><div class="avatar">♙</div><div><b>Nguyễn Văn A</b><small>SV123456</small></div></div>
    </header>

    <main class="success-layout">
        <section class="success-card">
            <div class="success-banner">
                <div class="check">✓</div>
                <div><h1>ĐẶT LỊCH HẸN THÀNH CÔNG!</h1><p>Thông tin lịch hẹn của bạn đã được ghi nhận.</p></div>
            </div>

            <div class="success-body">
                <div class="appointment-info">
                    <h2>THÔNG TIN LỊCH HẸN</h2>
                    <?php
                    $rows = [
                        ["Mã lịch hẹn", (string)$appointment["id"]],
                        ["Họ và tên", (string)$appointment["name"]],
                        ["Mã số sinh viên", (string)$appointment["student_id"]],
                        ["Email", (string)$appointment["email"]],
                        ["Số điện thoại", (string)$appointment["phone"]],
                        ["Giảng viên", (string)$appointment["lecturer"]],
                        ["Nội dung tư vấn", (string)$appointment["subject"]],
                        ["Hình thức gặp gỡ", (string)$appointment["meeting_type"]],
                        ["Ngày hẹn", formatDateVN((string)$appointment["date"])],
                        ["Khung giờ", (string)$appointment["time"]],
                        ["Nội dung chi tiết", $appointment["detail"] !== "" ? (string)$appointment["detail"] : "Không có"]
                    ];
                    foreach ($rows as $row):
                    ?>
                        <div class="info-row"><span><b><?= e($row[0]) ?></b></span><strong><?= e($row[1]) ?></strong></div>
                    <?php endforeach; ?>
                </div>

                <aside class="status-box">
                    <h2>TRẠNG THÁI<br>LỊCH HẸN</h2>
                    <div class="clock">◷</div>
                    <div class="status-pill">Chờ xác nhận</div>
                    <p>Lịch hẹn đang chờ giảng viên xác nhận.</p>
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

    <div class="note">ⓘ <b>Lưu ý:</b> Vui lòng có mặt trước giờ hẹn 5-10 phút.</div>
    <footer class="footer"><span>© EduMeet. All rights reserved.</span><span>Chính sách bảo mật</span><span>Hỗ trợ</span></footer>
</div>
</body>
</html>
